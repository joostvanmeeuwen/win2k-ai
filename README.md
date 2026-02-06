# LLM Desktop Client for Windows 2000 Professional

A retro AI chat application consisting of a Windows 2000 desktop client and a PHP proxy server that bridges legacy HTTP to modern LLM APIs.

## Architecture

```
┌─────────────────────┐         ┌─────────────────────┐         ┌─────────────────┐
│  Windows 2000 PC    │  HTTP   │    Linux Host       │  HTTPS  │   LLM APIs      │
│  (C# Client)        │ ──────> │    (PHP Proxy)      │ ──────> │  Claude/Gemini  │
└─────────────────────┘         └─────────────────────┘         └─────────────────┘
```

## Components

- **proxy/** - Symfony PHP application that proxies requests to Claude (Anthropic) and Gemini (Google) APIs
- **client/** - Windows 2000 compatible desktop application (C#, Visual Studio 2005)

---

## Proxy

### Requirements

- Docker & Docker Compose
- Anthropic API key and/or Google API key

### Setup

1. Clone the repository
2. Configure API keys in `proxy/.env`:
   ```
   ANTHROPIC_API_KEY=sk-ant-api03-...
   GOOGLE_API_KEY=AIza...
   ```
3. Start the containers:
   ```bash
   cd proxy
   docker compose up -d
   docker compose exec php composer install
   ```

### API Endpoints

#### GET /api/models

Returns a list of available LLM models.

**Request:**
```bash
curl http://localhost:8080/api/models
```

**Response:**
```json
{
  "models": [
    {"id": "claude-sonnet-4-20250514", "name": "Claude Sonnet 4", "provider": "anthropic"},
    {"id": "gemini-2.0-flash", "name": "Gemini 2.0 Flash", "provider": "google"}
  ]
}
```

#### POST /api/chat

Send a prompt to an LLM and receive a response.

**Parameters (form-urlencoded or JSON):**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| prompt | string | Yes | The user's message |
| model | string | Yes | Model ID (e.g., `claude-sonnet-4-20250514`) |
| back_in_time | bool | No | Enable "year 2000" mode (default: false) |

**Request (form-urlencoded, plain text response):**
```bash
curl -X POST http://localhost:8080/api/chat \
  -H "Accept: text/plain" \
  -d "prompt=Hello, who are you?&model=claude-sonnet-4-20250514"
```

**Response:**
```
Hello! I'm Claude, an AI assistant made by Anthropic. How can I help you today?
```

**Request (form-urlencoded, JSON response):**
```bash
curl -X POST http://localhost:8080/api/chat \
  -H "Accept: application/json" \
  -d "prompt=Hello&model=claude-sonnet-4-20250514"
```

**Response:**
```json
{
  "response": "Hello! How can I help you today?",
  "model": "claude-sonnet-4-20250514"
}
```

**Request (JSON input):**
```bash
curl -X POST http://localhost:8080/api/chat \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"prompt": "Hello", "model": "claude-sonnet-4-20250514", "back_in_time": false}'
```

**Request (with "Back in Time" mode):**
```bash
curl -X POST http://localhost:8080/api/chat \
  -H "Accept: text/plain" \
  -d "prompt=What do you think of the iPhone?&model=claude-sonnet-4-20250514&back_in_time=1"
```

When `back_in_time=1`, the LLM will roleplay as if it's the year 2000, with no knowledge of events or technology after December 31, 2000.

### Available Models

**Anthropic Claude:**
- `claude-opus-4-6` - Claude Opus 4.6
- `claude-opus-4-5-20251101` - Claude Opus 4.5
- `claude-sonnet-4-5-20250929` - Claude Sonnet 4.5
- `claude-haiku-4-5-20251001` - Claude Haiku 4.5
- `claude-opus-4-1-20250805` - Claude Opus 4.1
- `claude-opus-4-20250514` - Claude Opus 4
- `claude-sonnet-4-20250514` - Claude Sonnet 4
- `claude-3-5-haiku-20241022` - Claude Haiku 3.5
- `claude-3-haiku-20240307` - Claude Haiku 3

**Google Gemini:**
- `gemini-3-pro-preview` - Gemini 3 Pro Preview
- `gemini-3-flash-preview` - Gemini 3 Flash Preview
- `gemini-2.5-pro` - Gemini 2.5 Pro
- `gemini-2.5-flash` - Gemini 2.5 Flash
- `gemini-2.5-flash-lite` - Gemini 2.5 Flash-Lite
- `gemini-2.0-flash` - Gemini 2.0 Flash
- `gemini-2.0-flash-lite` - Gemini 2.0 Flash-Lite

---

## Client

Windows 2000 desktop application (coming soon).

### Requirements

- Windows 2000 Professional
- .NET Framework 1.1 or 2.0
- Microsoft Visual Studio 2005 (for development)

---

## License

MIT
