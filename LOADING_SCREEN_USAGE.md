# Global Loading Screen Documentation

The global loading screen system provides a beautiful, consistent loading experience across your entire Laravel application using the Lottie animation you specified.

## Features

- **Main Loading Screen**: Full-screen loading with Lottie animation
- **Mini Loading Bar**: Top progress bar for quick operations
- **Loading Spinner**: Compact spinner for buttons/cards
- **AJAX Integration**: Built-in AJAX helpers with loading states
- **Form Integration**: Automatic loading for forms
- **Progress Animation**: Animated progress bars for long operations

## Quick Usage Examples

### 1. Basic Loading Screen

```javascript
// Show main loading screen
GlobalLoading.show('Processing your request...');

// Hide loading screen
GlobalLoading.hide();

// Show with progress bar
GlobalLoading.show('Uploading file...', true);

// Update message
GlobalLoading.updateMessage('Almost done...');

// Update progress
GlobalLoading.updateProgress(75);

// Complete with progress animation
GlobalLoading.completeProgress();
```

### 2. Mini Loading Bar

```javascript
// Show top loading bar
GlobalLoading.showMiniBar();

// Hide mini bar
GlobalLoading.hideMiniBar();
```

### 3. Loading Spinner

```javascript
// Show spinner
GlobalLoading.showSpinner('Loading data...');

// Hide spinner
GlobalLoading.hideSpinner();
```

### 4. AJAX with Loading

```javascript
// GET request with loading
AjaxWithLoading.get('/api/data', {
    loadingType: 'main',
    loadingMessage: 'Fetching data...',
    success: function(response) {
        console.log('Success:', response);
    },
    error: function(error) {
        console.error('Error:', error);
    }
});

// POST request with progress
AjaxWithLoading.post('/api/save', { name: 'John', email: 'john@example.com' }, {
    loadingType: 'main',
    loadingMessage: 'Saving data...',
    showProgress: true,
    success: function(response) {
        Swal.fire('Success!', 'Data saved successfully', 'success');
    }
});
```

### 5. Form Integration

Add `data-loading` attribute to any form:

```html
<form action="/submit" method="POST" data-loading="main" data-loading-message="Submitting form...">
    @csrf
    <!-- form fields -->
    <button type="submit">Submit</button>
</form>
```

Or use JavaScript:

```javascript
// Submit form with loading
submitFormWithLoading(document.getElementById('myForm'), {
    loadingType: 'main',
    loadingMessage: 'Submitting...',
    showProgress: true,
    success: function(response) {
        // Handle success
    }
});
```

### 6. Link Integration

Add `data-loading` attribute to any link:

```html
<a href="/heavy-page" data-loading="mini" data-loading-message="Loading page...">
    Go to Heavy Page
</a>
```

## Loading Types

| Type | Description | Use Case |
|------|-------------|----------|
| `main` | Full-screen loading with Lottie animation | Long operations, form submissions |
| `mini` | Top progress bar | Page navigation, quick operations |
| `spinner` | Compact spinner | Button actions, card operations |
| `none` | No loading | Silent operations |

## Advanced Usage

### Custom AJAX Request

```javascript
AjaxWithLoading.request({
    url: '/api/custom',
    method: 'POST',
    data: { custom: 'data' },
    headers: {
        'Authorization': 'Bearer token'
    },
    loadingType: 'main',
    loadingMessage: 'Custom operation...',
    showProgress: true,
    timeout: 60000, // 60 seconds
    success: function(response) {
        // Handle success
    },
    error: function(error) {
        // Handle error
    }
});
```

### Manual Progress Control

```javascript
// Show loading with manual progress
GlobalLoading.show('Processing...', true);

// Update progress manually
GlobalLoading.updateProgress(25);
GlobalLoading.updateProgress(50);
GlobalLoading.updateProgress(75);

// Complete
GlobalLoading.completeProgress();
```

### Multiple Loading States

```javascript
// Chain operations with different loading types
async function processData() {
    try {
        // Step 1: Fetch data
        await AjaxWithLoading.get('/api/data', {
            loadingType: 'mini',
            loadingMessage: 'Fetching data...'
        });
        
        // Step 2: Process data
        await AjaxWithLoading.post('/api/process', data, {
            loadingType: 'main',
            loadingMessage: 'Processing data...',
            showProgress: true
        });
        
        // Step 3: Save results
        await AjaxWithLoading.post('/api/save', results, {
            loadingType: 'spinner',
            loadingMessage: 'Saving results...'
        });
        
    } catch (error) {
        console.error('Error:', error);
    }
}
```

## Styling Customization

The loading screen uses Tailwind CSS classes. You can customize the appearance by modifying the CSS in `components/loading-screen.blade.php`:

```css
/* Custom loading screen colors */
#globalLoadingScreen {
    /* Your custom styles */
}

#loadingContent {
    /* Your custom content styles */
}
```

## Global Variables

- `GlobalLoading` - Main loading controller
- `AjaxWithLoading` - AJAX helper with loading
- `LoadingScreen` - Alias for GlobalLoading

## Browser Support

- Modern browsers with ES6 support
- Chrome 61+
- Firefox 60+
- Safari 10.1+
- Edge 16+

## Troubleshooting

### Loading screen not showing
- Ensure the loading component is included in your layout
- Check browser console for JavaScript errors
- Verify the loading scripts are loaded

### Progress bar not animating
- Make sure `showProgress: true` is set
- Check that the progress bar element exists in the DOM

### AJAX requests failing
- Verify CSRF token is present
- Check network tab in browser dev tools
- Ensure server endpoints are accessible

## Best Practices

1. **Use appropriate loading types**: Match loading type to operation duration
2. **Provide meaningful messages**: Tell users what's happening
3. **Handle errors gracefully**: Always include error handling
4. **Set reasonable timeouts**: Prevent infinite loading states
5. **Test on slow connections**: Ensure loading works on all network conditions

## Integration with Existing Code

The loading system is designed to work with your existing codebase without breaking changes. Simply add the `data-loading` attributes to forms and links where you want loading states to appear.

For custom JavaScript operations, replace manual loading indicators with the GlobalLoading methods for consistency.
