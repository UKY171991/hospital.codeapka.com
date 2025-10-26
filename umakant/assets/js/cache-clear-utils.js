/**
 * Cache Clear Utilities
 * Provides cache management functionality
 */

window.CacheClearUtils = {
    clearBrowserCache: function() {
        // Clear browser cache where possible
        if ('caches' in window) {
            caches.keys().then(function(names) {
                names.forEach(function(name) {
                    caches.delete(name);
                });
            });
        }
    },
    
    reloadWithoutCache: function() {
        // Reload page without cache
        window.location.reload(true);
    }
};