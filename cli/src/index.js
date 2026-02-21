#!/usr/bin/env node

import inquirer from "inquirer";
import chalk from "chalk";
import open from "open";
import fs from "fs";
import path from "path";
import os from "os";

const PROXY_BASE_URL = process.env.APIPOD_PROXY_URL || "https://api.apipod.app";
const DASHBOARD_URL = process.env.APIPOD_DASHBOARD_URL || "https://apipod.app";

const CONFIG_DIR = path.join(os.homedir(), ".apipod");
const CONFIG_PATH = path.join(CONFIG_DIR, "config.json");
const CLAUDE_SETTINGS_PATH = path.join(os.homedir(), ".claude", "settings.json");
const OPENCODE_CONFIG_PATH = path.join(os.homedir(), ".config", "opencode", "opencode.json");
const OPENCODE_AUTH_PATH = path.join(os.homedir(), ".local", "share", "opencode", "auth.json");

// ── JSON helpers ──

function readJson(filePath) {
  try {
    return JSON.parse(fs.readFileSync(filePath, "utf-8"));
  } catch {
    return null;
  }
}

function writeJson(filePath, data) {
  const dir = path.dirname(filePath);
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true, mode: 0o700 });
  fs.writeFileSync(filePath, JSON.stringify(data, null, 2) + "\n", { encoding: "utf-8", mode: 0o600 });
}

function loadConfig() {
  return readJson(CONFIG_PATH) || {};
}

function saveConfig(cfg) {
  writeJson(CONFIG_PATH, cfg);
}

// ── Device Auth Login ──

async function login() {
  console.log(chalk.cyan("\n🔐 Login to Apipod\n"));
  console.log(chalk.gray(`  Dashboard: ${DASHBOARD_URL}\n`));

  // Request device code
  let deviceCode;
  try {
    const res = await fetch(`${DASHBOARD_URL}/api/auth/device/code`, { method: "POST" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    deviceCode = await res.json();
  } catch (err) {
    console.log(chalk.red(`  ✗ Failed to connect: ${err.message}`));
    return;
  }

  // Display code
  console.log(chalk.bold("  Open this URL in your browser:"));
  console.log(chalk.cyan.underline(`  ${deviceCode.verification_url}\n`));
  console.log(chalk.bold("  Enter this code:"));
  console.log(chalk.green.bold(`  ▶  ${deviceCode.user_code}  ◀\n`));

  // Try to open browser
  try {
    await open(deviceCode.verification_url);
    console.log(chalk.gray("  Browser opened automatically."));
  } catch {
    // ignore
  }

  // Poll for authorization
  const interval = (deviceCode.interval || 5) * 1000;
  const deadline = Date.now() + (deviceCode.expires_in || 600) * 1000;

  process.stdout.write(chalk.gray("  Waiting for authorization"));

  while (Date.now() < deadline) {
    await new Promise((r) => setTimeout(r, interval));
    process.stdout.write(".");

    try {
      const res = await fetch(`${DASHBOARD_URL}/api/auth/device/token`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ device_code: deviceCode.device_code }),
      });
      const data = await res.json();

      if (data.status === "authorized") {
        console.log("\n");
        const cfg = loadConfig();
        cfg.api_key = data.api_token;
        cfg.username = data.username;
        cfg.plan = data.plan;
        cfg.active_model = data.active_model;
        cfg.is_byok = data.is_byok;
        cfg.base_url = PROXY_BASE_URL;
        saveConfig(cfg);

        console.log(chalk.green.bold("  ✓ Authenticated successfully\n"));
        console.log(chalk.gray(`  Username: ${data.username}`));
        console.log(chalk.gray(`  Plan:     ${data.plan}`));
        if (data.active_model) {
          console.log(chalk.gray(`  Model:    ${data.active_model}`));
        } else if (data.is_byok) {
          console.log(chalk.yellow(`  ⚠ No model selected! Visit your dashboard to pick one.`));
        }
        console.log(chalk.gray(`  Config:   ${CONFIG_PATH}\n`));
        return;
      }

      if (data.status === "expired") {
        console.log("\n");
        console.log(chalk.red("  ✗ Device code expired. Please try again."));
        return;
      }
    } catch {
      // Network error, keep polling
    }
  }

  console.log("\n");
  console.log(chalk.red("  ✗ Login timed out. Please try again."));
}

// ── Logout ──

function logout() {
  const cfg = loadConfig();
  delete cfg.api_key;
  delete cfg.username;
  delete cfg.plan;
  delete cfg.active_model;
  delete cfg.is_byok;
  saveConfig(cfg);
  console.log(chalk.green("\n  ✓ Logged out successfully\n"));
}

// ── Who Am I ──

function whoami() {
  const cfg = loadConfig();
  if (!cfg.api_key) {
    console.log(chalk.yellow("\n  ⚠ Not logged in. Run login first.\n"));
    return;
  }
  console.log(`
  ${chalk.bold("👤 Account Info")}

  ${chalk.gray("Username")}  ${cfg.username || "unknown"}
  ${chalk.gray("Plan")}      ${cfg.plan || "unknown"}${cfg.active_model ? `\n  ${chalk.gray("Model")}     ${cfg.active_model}` : ""}
  ${chalk.gray("API URL")}   ${cfg.base_url || PROXY_BASE_URL}
  ${chalk.gray("Config")}    ${CONFIG_PATH}
`);
}

// ── Connect to Claude Code ──

function connectClaudeCode() {
  const cfg = loadConfig();
  if (!cfg.api_key) {
    console.log(chalk.yellow("\n  ⚠ Not logged in. Run login first.\n"));
    return;
  }

  const settings = readJson(CLAUDE_SETTINGS_PATH) || {};
  settings.env = settings.env || {};
  settings.env.ANTHROPIC_BASE_URL = cfg.base_url || PROXY_BASE_URL;
  settings.env.ANTHROPIC_API_KEY = cfg.api_key;
  writeJson(CLAUDE_SETTINGS_PATH, settings);

  console.log(chalk.green(`\n  ✓ Claude Code connected\n`));
  console.log(chalk.gray(`  Settings: ${CLAUDE_SETTINGS_PATH}`));
  console.log(chalk.gray(`  Base URL: ${settings.env.ANTHROPIC_BASE_URL}\n`));
}

// ── Connect to OpenCode ──

function connectOpenCode() {
  const cfg = loadConfig();
  if (!cfg.api_key) {
    console.log(chalk.yellow("\n  ⚠ Not logged in. Run login first.\n"));
    return;
  }

  const baseURL = cfg.base_url || PROXY_BASE_URL;

  // Auth
  const auth = readJson(OPENCODE_AUTH_PATH) || {};
  auth.anthropic = { type: "api", key: cfg.api_key };
  writeJson(OPENCODE_AUTH_PATH, auth);

  // Config
  const config = readJson(OPENCODE_CONFIG_PATH) || {};
  config["$schema"] = "https://opencode.ai/config.json";
  config.provider = config.provider || {};
  config.provider.anthropic = config.provider.anthropic || {};
  config.provider.anthropic.options = config.provider.anthropic.options || {};
  config.provider.anthropic.options.baseURL = `${baseURL}/v1`;
  writeJson(OPENCODE_CONFIG_PATH, config);

  console.log(chalk.green(`\n  ✓ OpenCode connected\n`));
  console.log(chalk.gray(`  Auth:   ${OPENCODE_AUTH_PATH}`));
  console.log(chalk.gray(`  Config: ${OPENCODE_CONFIG_PATH}`));
  console.log(chalk.gray(`  Base URL: ${baseURL}/v1\n`));
}

// ── Connect (sub-menu) ──

async function connect() {
  const { target } = await inquirer.prompt([
    {
      type: "list",
      name: "target",
      message: "Connect to:",
      choices: [
        { name: "🟠 Claude Code", value: "claude" },
        { name: "🔵 OpenCode", value: "opencode" },
        { name: "↩  Back", value: "back" },
      ],
    },
  ]);

  if (target === "claude") connectClaudeCode();
  else if (target === "opencode") connectOpenCode();
}

// ── Reset ──

async function reset() {
  const { target } = await inquirer.prompt([
    {
      type: "list",
      name: "target",
      message: "Reset settings for:",
      choices: [
        { name: "🟠 Claude Code", value: "claude" },
        { name: "🔵 OpenCode", value: "opencode" },
        { name: "↩  Back", value: "back" },
      ],
    },
  ]);

  if (target === "back") return;

  const { confirm } = await inquirer.prompt([
    {
      type: "confirm",
      name: "confirm",
      message: `Reset ${target === "claude" ? "Claude Code" : "OpenCode"} settings?`,
      default: false,
    },
  ]);

  if (!confirm) return;

  if (target === "claude") {
    const settings = readJson(CLAUDE_SETTINGS_PATH);
    if (settings?.env) {
      delete settings.env.ANTHROPIC_BASE_URL;
      delete settings.env.ANTHROPIC_API_KEY;
      if (Object.keys(settings.env).length === 0) delete settings.env;
      writeJson(CLAUDE_SETTINGS_PATH, settings);
      console.log(chalk.green("\n  ✓ Claude Code settings reset\n"));
    } else {
      console.log(chalk.yellow("\n  ⚠ No Claude Code proxy settings found\n"));
    }
  } else {
    let found = false;
    const config = readJson(OPENCODE_CONFIG_PATH);
    if (config?.provider?.anthropic?.options?.baseURL) {
      found = true;
      delete config.provider.anthropic.options.baseURL;
      if (Object.keys(config.provider.anthropic.options).length === 0) delete config.provider.anthropic.options;
      if (Object.keys(config.provider.anthropic).length === 0) delete config.provider.anthropic;
      if (Object.keys(config.provider).length === 0) delete config.provider;
      writeJson(OPENCODE_CONFIG_PATH, config);
    }
    const auth = readJson(OPENCODE_AUTH_PATH);
    if (auth?.anthropic) {
      found = true;
      delete auth.anthropic;
      writeJson(OPENCODE_AUTH_PATH, auth);
    }
    console.log(found ? chalk.green("\n  ✓ OpenCode settings reset\n") : chalk.yellow("\n  ⚠ No OpenCode proxy settings found\n"));
  }
}

// ── Help ──

function showHelp() {
  console.log(`
${chalk.bold.cyan("  apipod-cli")} — API Proxy Connector

${chalk.bold("  Commands:")}
    ${chalk.yellow("Login")}          Authenticate via device code (browser)
    ${chalk.yellow("Logout")}         Remove saved credentials
    ${chalk.yellow("Who Am I")}       Show current account info
    ${chalk.yellow("Connect")}        Inject config into Claude Code / OpenCode
    ${chalk.yellow("Reset")}          Remove proxy settings
    ${chalk.yellow("Help")}           Show this help

${chalk.bold("  Proxy URL:")} ${chalk.gray(PROXY_BASE_URL)}
${chalk.bold("  Config:")}    ${chalk.gray(CONFIG_PATH)}
`);
}

// ── Main Menu ──

async function main() {
  console.log(chalk.bold.cyan("\n  ◆ apipod-cli") + chalk.gray(" v1.0.0\n"));

  const cfg = loadConfig();
  if (cfg.api_key) {
    let status = `  Logged in as ${chalk.white(cfg.username || "unknown")} (${cfg.plan || "free"})`;
    if (cfg.active_model) {
      status += chalk.gray(` | Model: ${chalk.cyan(cfg.active_model)}`);
    } else if (cfg.is_byok) {
      status += chalk.yellow(` | ⚠ No model selected`);
    }
    console.log(status + "\n");
  } else {
    console.log(chalk.yellow("  ⚠ Not logged in") + chalk.gray(" — select Login to authenticate\n"));
  }

  while (true) {
    const { action } = await inquirer.prompt([
      {
        type: "list",
        name: "action",
        message: "What would you like to do?",
        choices: [
          { name: "🔐 Login", value: "login" },
          { name: "⚡ Connect", value: "connect" },
          { name: "👤 Who Am I", value: "whoami" },
          { name: "🔄 Reset", value: "reset" },
          { name: "🚪 Logout", value: "logout" },
          { name: "❓ Help", value: "help" },
          { name: "👋 Exit", value: "exit" },
        ],
      },
    ]);

    switch (action) {
      case "login":
        await login();
        break;
      case "connect":
        await connect();
        break;
      case "whoami":
        whoami();
        break;
      case "reset":
        await reset();
        break;
      case "logout":
        logout();
        break;
      case "help":
        showHelp();
        break;
      case "exit":
        console.log(chalk.gray("\n  Goodbye! 👋\n"));
        process.exit(0);
    }
  }
}

main().catch((err) => {
  if (err.name === "ExitPromptError") process.exit(0);
  console.error(chalk.red("Error:"), err.message);
  process.exit(1);
});
