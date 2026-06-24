import fs from 'fs';
import path from 'path';

function processDirectory(dirPath) {
    const files = fs.readdirSync(dirPath);
    for (const file of files) {
        const fullPath = path.join(dirPath, file);
        if (fs.statSync(fullPath).isDirectory()) {
            if (!fullPath.includes('emails')) {
                processDirectory(fullPath);
            }
        } else if (file.endsWith('.blade.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');

            // Strip any <link ... googleapis or cdnjs or cdn> multiline
            content = content.replace(/<link[^>]*href=["']https:\/\/(fonts\.googleapis|fonts\.gstatic|cdnjs\.cloudflare|cdn\.jsdelivr)[^>]*>/gi, '');
            
            // Strip FontAwesome with integrity that might span multiple lines
            content = content.replace(/<link[^>]*href=["']https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome[^>]*>(.*?|[\s\S]*?)<\/link>|<link[^>]*href=["']https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome[^>]*>/gi, '');

            // Strip <script ... cdnjs or cdn> </script>
            content = content.replace(/<script[^>]*src=["']https:\/\/(cdnjs\.cloudflare|cdn\.jsdelivr)[^>]*>.*?<\/script>/gi, '');

            // Ensure no empty ghost lines around what we just deleted (simple clean up)
            content = content.replace(/^\s*[\r\n]/gm, '\n');

            fs.writeFileSync(fullPath, content, 'utf8');
        }
    }
}

const dir = path.join(process.cwd(), 'resources/views');
processDirectory(dir);
console.log('CDN cleanup completed.');
