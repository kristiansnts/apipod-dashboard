#!/bin/bash
set -e

DASHBOARD_URL="${APIPOD_DASHBOARD_URL:-https://apipod.app}"
INSTALL_DIR="${APIPOD_INSTALL_DIR:-$HOME/.apipod}"
BIN_DIR="$INSTALL_DIR/bin"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
GRAY='\033[0;90m'
BOLD='\033[1m'
RESET='\033[0m'

info()  { echo -e "${CYAN}  ▶${RESET} $1"; }
ok()    { echo -e "${GREEN}  ✓${RESET} $1"; }
err()   { echo -e "${RED}  ✗${RESET} $1"; exit 1; }

echo ""
echo -e "${BOLD}${CYAN}  ◆ apipod-cli installer${RESET}"
echo ""

# Check Node.js
if ! command -v node &>/dev/null; then
  err "Node.js is required. Install it from https://nodejs.org"
fi

NODE_VERSION=$(node -v | sed 's/v//' | cut -d. -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
  err "Node.js 18+ is required (found v$(node -v))"
fi

ok "Node.js $(node -v)"

# Check npm
if ! command -v npm &>/dev/null; then
  err "npm is required"
fi

# Create install directory
info "Installing to $INSTALL_DIR"
mkdir -p "$BIN_DIR"

# Download latest release from dashboard
TEMP_DIR=$(mktemp -d)
trap 'rm -rf "$TEMP_DIR"' EXIT

info "Downloading apipod-cli from $DASHBOARD_URL..."

curl -fsSL "$DASHBOARD_URL/cli/download" -o "$TEMP_DIR/apipod-cli.tar.gz"
tar -xzf "$TEMP_DIR/apipod-cli.tar.gz" -C "$TEMP_DIR"

ok "Downloaded"

# Install dependencies
info "Installing dependencies..."
cd "$TEMP_DIR/apipod-cli"
npm install --production --silent 2>/dev/null
ok "Dependencies installed"

# Copy to install dir
rm -rf "$INSTALL_DIR/lib"
cp -r "$TEMP_DIR/apipod-cli" "$INSTALL_DIR/lib"

# Create launcher script
cat > "$BIN_DIR/apipod" << 'LAUNCHER'
#!/bin/bash
SCRIPT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
exec node "$SCRIPT_DIR/lib/src/index.js" "$@"
LAUNCHER
chmod +x "$BIN_DIR/apipod"

ok "Installed to $INSTALL_DIR"

# Add to PATH
SHELL_NAME=$(basename "$SHELL")
PROFILE=""

case "$SHELL_NAME" in
  zsh)  PROFILE="$HOME/.zshrc" ;;
  bash)
    if [ -f "$HOME/.bash_profile" ]; then
      PROFILE="$HOME/.bash_profile"
    else
      PROFILE="$HOME/.bashrc"
    fi
    ;;
  fish) PROFILE="$HOME/.config/fish/config.fish" ;;
esac

PATH_LINE="export PATH=\"$BIN_DIR:\$PATH\""
if [ "$SHELL_NAME" = "fish" ]; then
  PATH_LINE="set -gx PATH $BIN_DIR \$PATH"
fi

if [ -n "$PROFILE" ]; then
  if ! grep -q "$BIN_DIR" "$PROFILE" 2>/dev/null; then
    echo "" >> "$PROFILE"
    echo "# apipod-cli" >> "$PROFILE"
    echo "$PATH_LINE" >> "$PROFILE"
    ok "Added to PATH in $PROFILE"
  else
    ok "PATH already configured"
  fi
fi

echo ""
echo -e "${GREEN}${BOLD}  ✓ apipod-cli installed successfully!${RESET}"
echo ""
echo -e "${GRAY}  Run:${RESET}"
echo -e "    ${BOLD}apipod${RESET}              Launch the CLI"
echo -e "    ${BOLD}apipod --help${RESET}       Show help"
echo ""

if ! echo "$PATH" | grep -q "$BIN_DIR"; then
  echo -e "${GRAY}  Restart your terminal or run:${RESET}"
  echo -e "    ${BOLD}source $PROFILE${RESET}"
  echo ""
fi
