// Analytics Worker

let requestQueue = [];
const batchSize = 10;
const batchInterval = 1000; // 1 second

self.addEventListener('message', function(e) {
    if (e.data.type === 'track') {
        requestQueue.push(e.data.payload);
        if (requestQueue.length >= batchSize) {
            processBatch();
        }
    }
});

function processBatch() {
    if (requestQueue.length === 0) return;

    const batch = requestQueue.splice(0, batchSize);
    
    fetch(self.analyticsEndpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(batch)
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    }).then(data => {
        self.postMessage({ type: 'batchProcessed', success: true, count: batch.length });
    }).catch(error => {
        console.error('Error processing batch:', error);
        self.postMessage({ type: 'batchProcessed', success: false, error: error.message });
        // Re-add failed requests to the queue
        requestQueue.unshift(...batch);
    });
}

setInterval(processBatch, batchInterval);

self.postMessage({ type: 'workerReady' });
