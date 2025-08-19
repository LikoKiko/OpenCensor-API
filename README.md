# OpenCensorAPI - My Final Year Project

This is my school project for final year in Software Engineering.

## What is this?

OpenCensorAPI is a simple service made with PHP. It acts as a middle layer API to check Hebrew text for bad words. It connects to an AI service on Modal that uses the OpenCensor-Hebrew model. This API gives data to a separate frontend with user interface.

### What it can do

- Check one Hebrew text for bad words.
- Check up to 256 texts at once.
- Uses simple HTTP calls with JSON answers.
- Works with requests from other websites (CORS).
- Easy to put on any PHP host.

### How it works

**This API service (middle layer):**
- PHP code that handles requests from frontend.
- Manages web calls and sends to AI backend.
- Can go on any PHP server.

**AI backend:**
- See repo: https://github.com/LikoKIko/OpenCensor-Modal
- Uses FastAPI with fast computer help.
- Runs OpenCensor-Hebrew model from Hugging Face.
- Based on AlephBERT for Hebrew.
- 95% right, trained on over 3,000 Hebrew texts.
- Gives score from 0 (good) to 1 (bad).

## How to install

1. Get Composer.
2. Run `composer install` for needed files.
3. Copy `.env.example` to `.env` and add:
   ```
   OPENCENSOR_URL=https://your-ai-endpoint.com
   ```

## How to use the API

### POST /predict
Check one text.

**Ask:**
```json
{
  "text": "זה טקסט כלשהו :)"
}
```

**Answer:**
```json
{
  "data": [0.05, "CLEAN"]
}
```

### POST /batch
Check many texts (max 256).

**Ask:**
```json
{
  "texts": ["זה טקסט כלשהו 1", "זה טקסט כלשהו 2"]
}
```

### GET /
Info about API and ways.

## Project files

```
├── api/
│   └── index.php     # Main API code
├── includes/
│   └── oc.php        # Class to talk to AI
├── composer.json     # Needed PHP things
├── vercel.json       # For Vercel setup
├── .env.example      # Sample settings
└── README.md         # This file
```

## What you need

- GuzzleHttp: For web calls.
- vlucas/phpdotenv: For settings.

## How to put it online

Works on any PHP host with:
- PHP 7.4 or more.
- Composer.

Steps:
1. Upload files to server.
2. Run `composer install`.
3. Set `.env` with AI link.
4. Point server to api/ folder.

## Other related stuff

- AI Service: https://github.com/LikoKIko/OpenCensor-Modal
- Model: https://huggingface.co/LikoKIko/OpenCensor-Hebrew
- Demo: https://huggingface.co/spaces/LikoKIko/OpenCensor

## License

This is a school project for final year in Software Engineering.

**Give Credit**: If you use this project or part of it in public, business, or school work, you must give credit to the maker. Include:

- Author: LikoKiko
- Project: OpenCensorAPI - Hebrew Bad Words Checker
- Repository: https://github.com/LikoKiko/OpenCensor-API

**How to Use**:
- Free for school and research.
- Free to change and share if you give credit.
- Okay for business if you give credit.
- No promise it works, no blame if problems.

**Example Credit**:
```
Based on OpenCensorAPI by LikoKiko
Original repository: https://github.com/LikoKiko/OpenCensor-API
```