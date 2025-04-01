// ⚠️ Deinen eigenen API-Key hier eintragen
const API_KEY = 'AIzaSyBZnpHU7UDgHpIhrEDd1A631b8uVoezDds';

// URL zu deinem PHP-Skript auf deinem Server
const backendUrl = 'http://localhost/ProjektWebshop/WebShop/Backend/config/importbooks.php';

// Funktion zum Abrufen der Bücher von der Google Books API
async function fetchBooks(query, startIndex, maxResults) {
    const response = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&startIndex=${startIndex}&maxResults=${maxResults}&key=${API_KEY}`);
    const data = await response.json();
    return data.items || [];
}
const genres = ['fiction', 'thriller', 'fantasy', 'romance', 'science', 'history', 'horror', 'travel'];

// Maximal Bücher pro Genre (gesamt 200)
const booksPerGenre = 25;

// Google Books API-Anfrage
async function fetchBooks(query, maxResults) {
    const response = await fetch(`https://www.googleapis.com/books/v1/volumes?q=subject:${encodeURIComponent(query)}&maxResults=${maxResults}&key=${API_KEY}`);
    const data = await response.json();
    return data.items || [];
}

// Extrahiere Daten pro Buch
function parseBook(item, genre) {
    const volume = item.volumeInfo;

    return {
        title: volume.title || 'Unbekannter Titel',
        author: (volume.authors && volume.authors.join(', ')) || 'Unbekannter Autor',
        genre: genre,
        price: (Math.random() * (40 - 5) + 5).toFixed(2), // Zufallspreis zwischen 5 und 40 EUR
        image: volume.imageLinks?.thumbnail || '',
        publishedDate: volume.publishedDate || 'Unbekannt',
        description: volume.description || 'Keine Beschreibung verfügbar.',
        language: volume.language || 'unbekannt' // ← wichtige Ergänzung!
    };
}

// Hauptimportfunktion
async function importBooks() {
    const allBooks = [];

    for (let genre of genres) {
        console.log(`Importiere Genre: ${genre}`);
        const items = await fetchBooks(genre, booksPerGenre);
        items.forEach(item => allBooks.push(parseBook(item, genre)));
    }

    console.log("Gesamt importierte Bücher:", allBooks.length);

    // Senden aller Bücher ans Backend (PHP)
    const response = await fetch(backendUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(allBooks)
    });

    const result = await response.json();
    console.log(`Gespeichert: ${result.count} Bücher`);
    document.getElementById('result').textContent = `✅ Erfolgreich gespeichert: ${result.count} Bücher aus ${genres.length} Genres!`;
}

// Button-Event
document.getElementById('importBtn').addEventListener('click', () => {
    document.getElementById('result').textContent = '⏳ Bücher werden importiert...';
    importBooks().catch(error => {
        console.error("Fehler beim Import:", error);
        document.getElementById('result').textContent = '❌ Fehler beim Import! Prüfe die Konsole.';
    });
});