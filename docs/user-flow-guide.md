# APIPod — User Flow Guide

Welcome to APIPod! This guide walks you through everything from first signup to making your first API call.

---

## 🚀 Step 1: Register & Login

1. Go to `http://localhost:8000`
2. Click **Register** or sign in with Google
3. On registration, an **Organization** is automatically created for you
4. You start with **no plan** — you'll need to pick one

---

## 📦 Step 2: Choose a Plan

Navigate to **Subscription** in the sidebar.

| Plan | Price | Tokens | API Keys | Models |
|------|-------|--------|----------|--------|
| Free (BYOK) | Rp 0 | Bring your own key | 1 | All |
| Pro | Rp 79.000 | 3,000,000/month | 3 | Google models |

1. Click on a plan to purchase
2. Complete payment via Midtrans
3. After payment, your org gets:
   - `token_balance` = plan's token quota
   - `quota_reset_at` = 30 days from now
   - Access to plan's allowed models

---

## 🔑 Step 3: Create an API Key

Navigate to **API Keys** in the sidebar.

1. Enter a name (e.g. "Production", "Testing")
2. Optionally set a **token limit** per key
3. Click **Create Key**
4. ⚠️ **Copy the key immediately** — it's shown only once!

The key format: `apipod_xxxxxxxxxxxxxxxxxxxxxxxxxxxx`

You can see:
- Active/Revoked status
- Usage per key
- Per-key token limit bar (if set)

---

## 💻 Step 4: Make API Calls

Use your API key with the APIPod proxy endpoint. The proxy is compatible with OpenAI's API format:

```bash
curl -X POST https://your-apipod-proxy.com/v1/chat/completions \
  -H "Authorization: Bearer apipod_your_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "gemini-pro",
    "messages": [
      {"role": "user", "content": "Hello!"}
    ]
  }'
```

### What happens behind the scenes:

```
Your Request
    │
    ▼
┌─────────────────────┐
│ Go Proxy receives    │
│ your request         │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Pre-check            │
│ ✓ Org active?        │
│ ✓ Balance > 0?       │
│ ✓ Key active?        │
│ ✓ Key within limit?  │
│ ✓ Model allowed?     │
└──────┬──────────────┘
       │ Pass ✅
       ▼
┌─────────────────────┐
│ Forward to upstream  │
│ provider (Google, etc)│
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ Commit usage         │
│ • Deduct real tokens │
│ • Record ledger      │
│ • Update key usage   │
└─────────────────────┘
```

If any pre-check fails, you get:
- **429** — quota exceeded or key limit reached
- **403** — model not allowed on your plan

---

## 📊 Step 5: Monitor Usage

### Usage & Quotas Page
Navigate to **Usage & Quotas** in the sidebar to see:
- Your token usage log (per request)
- Ledger entries (usage, topups, resets)

### Plan Status Page
Navigate to **Plan Status** to see:
- Current plan details
- **Token quota bar** — visual remaining balance
- Quota reset date
- Rate limits (RPM/TPM)
- List of models available on your plan

### API Keys Page
Check per-key usage:
- `used_tokens` per key
- Per-key limit progress bar
- Last used timestamp

---

## 🔄 Step 6: Quota Reset (Automatic)

Every billing cycle (default 30 days):
1. Your `token_balance` refills to the plan's `token_quota`
2. All per-key `used_tokens` reset to 0
3. A `reset` entry appears in your ledger

This runs automatically via daily cron. No action needed.

---

## ⚠️ Common Scenarios

### "429 — Token quota exceeded"
Your org's token balance is 0 or negative.
- **Check**: Plan Status page → quota bar
- **Fix**: Wait for quota reset, or ask admin for manual topup, or upgrade plan

### "403 — Model not allowed"
You're requesting a model not included in your plan.
- **Check**: Plan Status page → Available Models
- **Fix**: Use an allowed model, or upgrade to a plan that includes it

### "API key token limit exceeded"
Your specific key hit its per-key cap.
- **Check**: API Keys page → key usage bar
- **Fix**: Use a different key, or create one with a higher limit

### "API key is inactive"
Your key was revoked.
- **Fix**: Create a new key from the API Keys page

---

## 🧑‍💼 For Admins

If you have access to the Filament admin panel (`/admin`):

| Resource | What You Can Do |
|----------|----------------|
| Organizations | View balance, add tokens, block/unblock |
| Token Ledger | View all usage, cost, revenue across orgs |
| Plans | Set token_quota, max_api_keys, rate limits, allowed models |
| Providers | Enable/disable providers |
| Models | Set pricing, tool support, context window, routing weight |
