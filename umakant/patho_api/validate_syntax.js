// Simple syntax validation test
const fs = require('fs');

try {
    const content = fs.readFileSync('api.html', 'utf8');
    
    // Extract JavaScript content between <script> tags
    const scriptMatch = content.match(/<script>([\s\S]*?)<\/script>/);
    
    if (scriptMatch) {
        const jsContent = scriptMatch[1];
        
        // Try to create a function with the JS content to validate syntax
        try {
            new Function(jsContent);
            console.log('✅ JavaScript syntax is valid');
        } catch (syntaxError) {
            console.log('❌ JavaScript syntax error:', syntaxError.message);
            
            // Try to find the approximate line number
            const lines = jsContent.split('\n');
            console.log('Total JS lines:', lines.length);
        }
    } else {
        console.log('❌ No script tag found');
    }
    
} catch (error) {
    console.log('❌ Error reading file:', error.message);
}
