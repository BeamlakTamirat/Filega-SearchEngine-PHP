/**
 * Filega Search Engine - Main JavaScript
 */

// Global variables
let suggestionTimeout;
const MIN_CHARS_FOR_SUGGESTIONS = 2;

/**
 * Document ready function
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search input functionality
    initSearchInput();
    
    // Initialize bookmark functionality if on search results page
    initBookmarkButtons();
});

/**
 * Initialize search input functionality
 */
function initSearchInput() {
    const searchInput = document.getElementById('search-input');
    const searchInputResults = document.getElementById('search-input-results');
    const suggestionsContainer = document.getElementById('search-suggestions');
    
    // Add event listeners to main search input
    if (searchInput) {
        // Focus input on page load
        searchInput.focus();
        
        // Handle input for suggestions
        searchInput.addEventListener('input', function() {
            handleSearchInput(this.value, suggestionsContainer);
        });
        
        // Handle focus event
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= MIN_CHARS_FOR_SUGGESTIONS) {
                handleSearchInput(this.value, suggestionsContainer);
            }
        });
        
        // Handle blur event
        searchInput.addEventListener('blur', function() {
            // Small delay to allow clicking on suggestions
            setTimeout(() => {
                suggestionsContainer.style.display = 'none';
            }, 200);
        });
        
        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            handleKeyboardNavigation(e, suggestionsContainer);
        });
    }
    
    // Add event listeners to results page search input
    if (searchInputResults) {
        searchInputResults.addEventListener('input', function() {
            handleSearchInput(this.value, suggestionsContainer);
        });
        
        searchInputResults.addEventListener('focus', function() {
            if (this.value.length >= MIN_CHARS_FOR_SUGGESTIONS) {
                handleSearchInput(this.value, suggestionsContainer);
            }
        });
        
        searchInputResults.addEventListener('blur', function() {
            setTimeout(() => {
                suggestionsContainer.style.display = 'none';
            }, 200);
        });
        
        searchInputResults.addEventListener('keydown', function(e) {
            handleKeyboardNavigation(e, suggestionsContainer);
        });
    }
}

/**
 * Handle search input and show suggestions
 */
function handleSearchInput(value, suggestionsContainer) {
    if (!suggestionsContainer) return;
    
    // Clear previous timeout
    clearTimeout(suggestionTimeout);
    
    // If the input is empty or too short, hide suggestions
    if (!value || value.length < MIN_CHARS_FOR_SUGGESTIONS) {
        suggestionsContainer.style.display = 'none';
        return;
    }
    
    // Set timeout to avoid too many requests
    suggestionTimeout = setTimeout(() => {
        fetchSuggestions(value, suggestionsContainer);
    }, 300);
}

/**
 * Fetch search suggestions from API
 */
function fetchSuggestions(query, suggestionsContainer) {
    fetch(`api/suggestions.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.suggestions.length > 0) {
                showSuggestions(data.suggestions, suggestionsContainer);
            } else {
                suggestionsContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
            suggestionsContainer.style.display = 'none';
        });
}

/**
 * Show suggestions in dropdown
 */
function showSuggestions(suggestions, container) {
    // Clear previous suggestions
    container.innerHTML = '';
    
    // Add new suggestions
    suggestions.forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.textContent = suggestion;
        
        // Handle click on suggestion
        item.addEventListener('click', function() {
            const searchInput = document.getElementById('search-input') || document.getElementById('search-input-results');
            if (searchInput) {
                searchInput.value = suggestion;
                searchInput.form.submit();
            }
        });
        
        container.appendChild(item);
    });
    
    // Show container
    container.style.display = 'block';
}

/**
 * Handle keyboard navigation in suggestions
 */
function handleKeyboardNavigation(event, suggestionsContainer) {
    if (!suggestionsContainer || suggestionsContainer.style.display === 'none') return;
    
    const items = suggestionsContainer.getElementsByClassName('suggestion-item');
    const activeClass = 'active';
    let activeItem = suggestionsContainer.querySelector('.' + activeClass);
    let activeIndex = activeItem ? Array.from(items).indexOf(activeItem) : -1;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            if (activeItem) {
                activeItem.classList.remove(activeClass);
            }
            activeIndex = (activeIndex + 1) % items.length;
            items[activeIndex].classList.add(activeClass);
            break;
        
        case 'ArrowUp':
            event.preventDefault();
            if (activeItem) {
                activeItem.classList.remove(activeClass);
            }
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            items[activeIndex].classList.add(activeClass);
            break;
        
        case 'Enter':
            if (activeItem) {
                event.preventDefault();
                activeItem.click();
            }
            break;
        
        case 'Escape':
            suggestionsContainer.style.display = 'none';
            break;
    }
}

/**
 * Load recent searches
 */
function loadRecentSearches() {
    const recentSearchesContainer = document.getElementById('recent-searches');
    if (!recentSearchesContainer) return;
    
    fetch('api/search_history.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.history.length > 0) {
                renderRecentSearches(data.history, recentSearchesContainer);
            } else {
                recentSearchesContainer.innerHTML = '<div class="no-recent-searches">No recent searches found</div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent searches:', error);
            recentSearchesContainer.innerHTML = '<div class="error">Failed to load recent searches</div>';
        });
}

/**
 * Render recent searches in UI
 */
function renderRecentSearches(history, container) {
    container.innerHTML = '';
    
    history.forEach(item => {
        const date = new Date(item.search_date);
        const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        
        const searchItem = document.createElement('div');
        searchItem.className = 'recent-search-item';
        searchItem.setAttribute('data-id', item.id);
        searchItem.setAttribute('data-query', item.query);
        
        searchItem.innerHTML = `
            <div class="query">${item.query}</div>
            <div class="date">${formattedDate}</div>
            <button class="delete-btn" title="Delete from history">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add click handler for search item
        searchItem.addEventListener('click', function(e) {
            // If click is not on delete button, perform search
            if (!e.target.closest('.delete-btn')) {
                window.location.href = `search.php?q=${encodeURIComponent(this.getAttribute('data-query'))}`;
            }
        });
        
        // Add click handler for delete button
        const deleteBtn = searchItem.querySelector('.delete-btn');
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            deleteSearchHistory(item.id, searchItem);
        });
        
        container.appendChild(searchItem);
    });
}

/**
 * Delete a search history item
 */
function deleteSearchHistory(id, element) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('api/search_history.php?action=delete', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.remove();
                
                // Check if there are no more items
                const container = document.getElementById('recent-searches');
                if (container && container.children.length === 0) {
                    container.innerHTML = '<div class="no-recent-searches">No recent searches found</div>';
                }
            } else {
                console.error('Failed to delete search history item');
            }
        })
        .catch(error => {
            console.error('Error deleting search history:', error);
        });
}

/**
 * Initialize bookmark buttons
 */
function initBookmarkButtons() {
    const bookmarkButtons = document.querySelectorAll('.bookmark-btn');
    
    bookmarkButtons.forEach(button => {
        button.addEventListener('click', function() {
            const resultCard = this.closest('.result-card');
            const title = resultCard.querySelector('.result-title a').textContent;
            const url = resultCard.querySelector('.result-title a').getAttribute('href');
            const description = resultCard.querySelector('.result-snippet').textContent;
            
            saveBookmark(title, url, description, this);
        });
    });
}

/**
 * Save a bookmark
 */
function saveBookmark(title, url, description, button) {
    const formData = new FormData();
    formData.append('title', title);
    formData.append('url', url);
    formData.append('description', description);
    
    fetch('api/bookmarks.php?action=add', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                button.classList.add('bookmarked');
                button.querySelector('i').classList.remove('far');
                button.querySelector('i').classList.add('fas');
                button.setAttribute('title', 'Bookmarked');
                
                // Show success message
                showNotification('Bookmark saved successfully', 'success');
            } else {
                showNotification('Failed to save bookmark', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving bookmark:', error);
            showNotification('Error saving bookmark', 'error');
        });
}

/**
 * Show a notification message
 */
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Append to body
    document.body.appendChild(notification);
    
    // Add visible class after a small delay for transition
    setTimeout(() => {
        notification.classList.add('visible');
    }, 10);
    
    // Remove after timeout
    setTimeout(() => {
        notification.classList.remove('visible');
        
        // Remove from DOM after transition
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
} 