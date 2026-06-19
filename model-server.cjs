// Static file server for AI model files
// Runs on port 8001 to avoid conflict with PHP artisan serve (port 8000)
// This way, HP can download model files without waiting for PHP server

const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = 8001;
const MODELS_DIR = path.join(__dirname, 'public', 'models');

// MIME types for model files
const MIME_TYPES = {
    '.json': 'application/json',
    '.bin': 'application/octet-stream',
};

const server = http.createServer((req, res) => {
    // CORS headers - allow access from any origin (PHP server on 8000)
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', '*');
    res.setHeader('Cache-Control', 'public, max-age=86400'); // Cache 24 hours

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    if (req.method !== 'GET') {
        res.writeHead(405);
        res.end('Method not allowed');
        return;
    }

    // Sanitize path to prevent directory traversal
    const fileName = path.basename(req.url);
    const filePath = path.join(MODELS_DIR, fileName);

    // Security: ensure file is in models directory
    if (!filePath.startsWith(MODELS_DIR)) {
        res.writeHead(403);
        res.end('Forbidden');
        return;
    }

    fs.stat(filePath, (err, stats) => {
        if (err || !stats.isFile()) {
            res.writeHead(404);
            res.end('File not found');
            return;
        }

        const ext = path.extname(filePath);
        const contentType = MIME_TYPES[ext] || 'application/octet-stream';

        res.writeHead(200, {
            'Content-Type': contentType,
            'Content-Length': stats.size,
        });

        fs.createReadStream(filePath).pipe(res);
    });
});

server.listen(PORT, '0.0.0.0', () => {
    console.log(`[Model Server] Serving AI models on http://0.0.0.0:${PORT}`);
    console.log(`[Model Server] Models directory: ${MODELS_DIR}`);
    console.log(`[Model Server] CORS enabled - accessible from any origin`);
    console.log(`[Model Server] Press Ctrl+C to stop`);
});
