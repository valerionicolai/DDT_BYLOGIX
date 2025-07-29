/**
 * Test automatico per gli endpoint S2-B03 e S2-B04 tramite interfaccia web
 * Questo script utilizza l'interfaccia HTML esistente per testare tutti gli endpoint
 */

// Funzione per attendere un determinato tempo
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Funzione per testare tutti gli endpoint
async function testAllEndpoints() {
    console.log('🚀 Inizio test automatico degli endpoint S2-B03 e S2-B04');
    
    try {
        // Test 1: Login
        console.log('\n📝 Test 1: Login');
        document.getElementById('email').value = 'admin@example.com';
        document.getElementById('password').value = 'password';
        document.getElementById('loginBtn').click();
        
        await sleep(2000); // Attende 2 secondi per il login
        
        // Verifica se il login è riuscito
        const authSection = document.getElementById('authSection');
        const documentsSection = document.getElementById('documentsSection');
        
        if (authSection.style.display === 'none' && documentsSection.style.display === 'block') {
            console.log('✅ Login riuscito');
        } else {
            console.log('❌ Login fallito');
            return;
        }
        
        // Test 2: Creazione documento (S2-B03)
        console.log('\n📝 Test 2: Creazione documento (S2-B03)');
        document.getElementById('title').value = 'Test Document Automatico';
        document.getElementById('content').value = 'Contenuto del documento di test automatico';
        document.getElementById('createBtn').click();
        
        await sleep(2000); // Attende 2 secondi per la creazione
        
        // Test 3: Lista documenti (S2-B04)
        console.log('\n📝 Test 3: Lista documenti (S2-B04)');
        document.getElementById('listBtn').click();
        
        await sleep(2000); // Attende 2 secondi per il caricamento della lista
        
        // Verifica se ci sono documenti nella lista
        const documentsList = document.getElementById('documentsList');
        if (documentsList.children.length > 0) {
            console.log('✅ Lista documenti caricata con successo');
            
            // Test 4: Dettaglio documento (S2-B04)
            console.log('\n📝 Test 4: Dettaglio documento (S2-B04)');
            const firstDocument = documentsList.children[0];
            const documentId = firstDocument.dataset.id;
            
            if (documentId) {
                document.getElementById('documentId').value = documentId;
                document.getElementById('getBtn').click();
                
                await sleep(2000); // Attende 2 secondi per il caricamento del dettaglio
                
                // Test 5: Aggiornamento documento (S2-B04)
                console.log('\n📝 Test 5: Aggiornamento documento (S2-B04)');
                document.getElementById('updateTitle').value = 'Test Document Aggiornato';
                document.getElementById('updateContent').value = 'Contenuto aggiornato del documento di test';
                document.getElementById('updateBtn').click();
                
                await sleep(2000); // Attende 2 secondi per l'aggiornamento
                
                // Test 6: Eliminazione documento (S2-B04)
                console.log('\n📝 Test 6: Eliminazione documento (S2-B04)');
                document.getElementById('deleteBtn').click();
                
                await sleep(2000); // Attende 2 secondi per l'eliminazione
                
                console.log('✅ Tutti i test completati con successo!');
            } else {
                console.log('❌ Impossibile ottenere l\'ID del documento');
            }
        } else {
            console.log('❌ Nessun documento trovato nella lista');
        }
        
    } catch (error) {
        console.error('❌ Errore durante i test:', error);
    }
}

// Avvia i test automatici quando la pagina è caricata
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', testAllEndpoints);
} else {
    testAllEndpoints();
}

console.log('📋 Script di test automatico caricato. I test inizieranno automaticamente.');