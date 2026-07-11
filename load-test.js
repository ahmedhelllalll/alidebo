import http from 'k6/http';
import { check, sleep } from 'k6';

// Run with: k6 run load-test.js
// If k6 is not installed, download it from https://k6.io/

export const options = {
    stages: [
        { duration: '30s', target: 20 }, // Ramp up to 20 users
        { duration: '1m', target: 20 },  // Stay at 20 users for 1 minute
        { duration: '30s', target: 0 },  // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<500'], // 95% of requests should be below 500ms
        http_req_failed: ['rate<0.01'],   // Error rate should be less than 1%
    },
};

const BASE_URL = 'http://127.0.0.1:8000'; // Change to your local or staging server URL

export default function () {
    // 1. Test Home Page (Performance/Caching)
    let resHome = http.get(`${BASE_URL}/`);
    check(resHome, {
        'home page status is 200': (r) => r.status === 200,
        'home page loads within 500ms': (r) => r.timings.duration < 500,
    });
    sleep(1);

    // 2. Test Directory Page (Performance/Caching)
    let resDirectory = http.get(`${BASE_URL}/directory`);
    check(resDirectory, {
        'directory page status is 200': (r) => r.status === 200,
        'directory page loads within 500ms': (r) => r.timings.duration < 500,
    });
    sleep(1);

    // 3. Test Live Search (Performance/Load under concurrent searches)
    let resSearch = http.get(`${BASE_URL}/directory/search?q=test`);
    check(resSearch, {
        'live search status is 200': (r) => r.status === 200,
        'live search returns JSON': (r) => r.headers['Content-Type'] && r.headers['Content-Type'].includes('application/json'),
        'live search loads within 300ms': (r) => r.timings.duration < 300,
    });
    sleep(1);
    
    // 4. Test Contact Page Route Form rendering
    let resContact = http.get(`${BASE_URL}/contact`);
    check(resContact, {
        'contact page status is 200': (r) => r.status === 200,
    });
    sleep(1);
}
