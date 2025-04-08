document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categoryFilter');
    const eventListDiv = document.getElementById('eventList');
  
    function fetchEvents() {
      const query = searchInput.value.trim();
      const category = categorySelect.value;
      // Construct request URL with query parameters
      let url = 'search.php?';
      if (query) {
        url += 'term=' + encodeURIComponent(query);
      }
      if (category) {
        url += (query ? '&' : '') + 'category=' + encodeURIComponent(category);
      }
      // Fetch data from the server
      fetch(url)
        .then(response => response.json())
        .then(data => {
          // data is expected to be an array of event objects
          if (data.length === 0) {
            eventListDiv.innerHTML = '<p>No events found.</p>';
            return;
          }
          // Build HTML for the filtered events
          let html = '';
          data.forEach(event => {
            html += '<div class="event-item">';
            if (event.image) {
              html += '<img src="uploads/' + event.image + '" alt="' + event.title + '" class="event-img">';
            }
            html += '<h3>' + event.title + '</h3>';
            html += '<p><strong>Date:</strong> ' + event.event_date + 
                    ' | <strong>Location:</strong> ' + event.location + 
                    ' | <strong>Category:</strong> ' + event.category + '</p>';
            html += '<p>' + event.description + '</p>';
            html += '</div>';
          });
          eventListDiv.innerHTML = html;
        })
        .catch(error => {
          console.error('Error fetching events:', error);
        });
    }
  
    // Attach event listeners for search input and category dropdown
    searchInput.addEventListener('input', fetchEvents);
    categorySelect.addEventListener('change', fetchEvents);
  });
  