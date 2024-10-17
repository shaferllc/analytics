const fs = require('fs');
const path = require('path');

// Define the input files and output file
const inputFiles = [
    'resources/js/analytics/utils.js',
    'resources/js/analytics/event_listeners.js',
    'resources/js/analytics/script.js'
];
const outputFile = 'resources/js/analytics/compiled.js';

// Function to read file contents
function readFile(filePath) {
    return new Promise((resolve, reject) => {
        fs.readFile(filePath, 'utf8', (err, data) => {
            if (err) reject(err);
            else resolve(data);
        });
    });
}

// Function to write compiled content to file
function writeFile(filePath, content) {
    return new Promise((resolve, reject) => {
        fs.writeFile(filePath, content, 'utf8', (err) => {
            if (err) reject(err);
            else resolve();
        });
    });
}

// Main compilation function
async function compileFiles() {
    try {
        let compiledContent = '';

        // Read and concatenate all input files
        for (const file of inputFiles) {
            const content = await readFile(file);
            compiledContent += `// File: ${path.basename(file)}\n${content}\n\n`;
        }

        // Write the compiled content to the output file
        await writeFile(outputFile, compiledContent);

        console.log(`Compilation successful. Output written to ${outputFile}`);
    } catch (error) {
        console.error('Compilation failed:', error);
    }
}

// Run the compilation
compileFiles();


//  node compile.js