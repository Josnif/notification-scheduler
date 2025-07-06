# Notification Scheduler WordPress Plugin

A multipurpose WordPress plugin that displays customizable notifications with flexible variable support. Perfect for product notifications, social proof, or any dynamic content display.

## Features

- **Customizable Intervals**: Set the delay before showing popup notifications
- **Flexible Variable System**: Support for arrays, text, numbers, and ranges
- **Curly Bracket Variables**: Use `{variable_name}` placeholders in text templates
- **Dynamic Content**: Randomly processes variables for varied notifications
- **Responsive Design**: Works on desktop and mobile devices
- **Admin Settings**: Easy-to-use WordPress admin interface for configuration
- **Multipurpose**: Use for product notifications, social proof, announcements, etc.

## Installation

1. Upload the plugin files to `/wp-content/plugins/notification-scheduler/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Settings' > 'Notification Scheduler' to configure the plugin

## Configuration

### Basic Settings

- **Interval**: Set the delay in seconds before showing the first popup (default: 30 seconds)
- **Text Template**: Use `{variable_name}` as placeholders for variables (default: "Someone from {city} just purchased {product}")

### Variables

Add multiple variables with the following types:

- **Array**: Multiple values separated by `|` (e.g., "New York|Los Angeles|Chicago")
- **Text**: Single text value
- **Number**: Random number between 1-100
- **Range**: Custom number range (e.g., "5-15" for random number between 5 and 15)

## Variable Types

### Array Variables
Separate multiple values with the `|` character:
```
New York|Los Angeles|Chicago|Miami|Seattle
```

### Text Variables
Single static value:
```
Premium Widget
```

### Number Variables
Automatically generates random numbers between 1-100.

### Range Variables
Specify custom ranges:
```
5-15    // Random number between 5 and 15
1-10    // Random number between 1 and 10
```

## Usage Examples

### Product Notifications
**Text Template**: `"Someone from {city} just purchased {product} for ${price}"`

**Variables**:
- `city`: `New York|Los Angeles|Chicago|Miami|Seattle`
- `product`: `Premium Widget|Super Gadget|Amazing Tool|Best Product`
- `price`: `29|49|79|99`

**Result**: "Someone from Miami just purchased Super Gadget for $49"

### Social Proof
**Text Template**: `"{name} from {city} just left a {rating}-star review!"`

**Variables**:
- `name`: `Sarah|Mike|Emma|John|Lisa`
- `city`: `New York|Los Angeles|Chicago`
- `rating`: `4|5`

**Result**: "Mike from Chicago just left a 5-star review!"

### Time-based Notifications
**Text Template**: `"Ordered {time} minutes ago"`

**Variables**:
- `time`: `5-15`

**Result**: "Ordered 12 minutes ago"

## File Structure

```
notification-scheduler/
├── notification-scheduler.php          # Main plugin file
├── includes/
│   └── class-notification-scheduler.php # Main plugin class
├── assets/
│   ├── js/
│   │   └── popup.js                    # Frontend JavaScript
│   └── css/
│       └── popup.css                   # Frontend styles
└── README.md                           # This file
```

## Technical Details

### Frontend Implementation

The plugin uses vanilla JavaScript to handle dynamic content:

- **Variable Processing**: Processes different variable types (array, text, number, range)
- **Template Engine**: Replaces `{variable_name}` placeholders with processed values
- **Random Selection**: Randomly selects values from arrays and generates random numbers
- **Responsive Design**: CSS Grid and Flexbox for responsive layout

### Admin Interface

- **WordPress Settings API**: Uses WordPress native settings framework
- **Dynamic Forms**: JavaScript-powered dynamic form fields for variables
- **Data Validation**: Server-side validation for all settings
- **Type-specific UI**: Different input fields based on variable type

### Performance

- **Lazy Loading**: Scripts and styles only load when needed
- **Efficient DOM**: Minimal DOM manipulation for better performance
- **Memory Management**: Proper cleanup of timers and event listeners

## Customization

### Styling

You can customize the popup appearance by modifying `assets/css/popup.css`:

```css
.ns-popup {
    /* Custom styles */
    background-color: #your-color;
    border-radius: 10px;
}

.ns-icon {
    color: #your-icon-color;
}
```

### JavaScript Customization

The popup behavior can be customized by modifying `assets/js/popup.js`:

```javascript
// Custom timing
this.showTimer = setTimeout(() => {
    this.show();
}, customDelay);

// Custom variable processing
processVariable(varName, varData) {
    // Your custom logic
}
```

## Use Cases

- **E-commerce**: Product purchase notifications
- **Social Proof**: Customer reviews and testimonials
- **Lead Generation**: Recent signup notifications
- **Content Marketing**: Recent article reads
- **Event Promotion**: Ticket sales notifications
- **Service Business**: Appointment bookings

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- jQuery (included with WordPress)

## Support

For support and feature requests, please create an issue in the plugin repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Multipurpose notification system
- Flexible variable support (array, text, number, range)
- Curly bracket variable processing
- Admin settings interface
- Responsive design 