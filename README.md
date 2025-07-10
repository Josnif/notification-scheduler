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
- **WooCommerce Integration**: Use WooCommerce product data in notifications
- **Text Formatting**: Use `*bold*` and `_italic_` in your templates
- **Popup Position & Effect**: Choose left or right position and popup animation effect (see below)

## Installation

1. Upload the plugin files to `/wp-content/plugins/notification-scheduler/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Settings' > 'Notification Scheduler' to configure the plugin

## Configuration

### Basic Settings

- **Interval**: Set the delay in seconds before showing the first popup (default: 30 seconds)
- **Text Template**: Use `{variable_name}` as placeholders for variables (default: "Someone from {city} just purchased {product}")
- **Template**: Choose between "Custom" and "WooCommerce Product Notification"
- **Popup Position**: Choose whether the popup appears on the left or right side of the screen
- **Popup Effect**: Choose the popup animation effect (e.g., fade, slide)

### Variables

Add multiple variables with the following types:

- **Array**: Multiple values separated by `|` (e.g., "New York|Los Angeles|Chicago")
- **Text**: Single text value
- **Number**: Random number between 1-100
- **Range**: Custom number range (e.g., "5-15" for random number between 5 and 15)
- **Image URL**: (Optional) Show a custom image for a variable

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

### Image URL
If provided, this image will be shown in the popup for this variable. If not, a default icon is shown.

## Text Formatting

You can use special formatting in your notification templates:
- `*text*` or `*{variable}*` for **bold**
- `_text_` or `_{variable}_` for *italic*

Example:
```
*_{product}_* just bought by *{buyer}*!
```

## Popup Position & Effect

You can configure the popup to appear on the left or right side of the screen, and choose the animation effect:
- **Popup Position**: `Left` or `Right` (default: Left)
- **Popup Effect**: `Fade` or `Slide` (default: Fade)

---

## Usage Examples

### Product Notifications (Custom Template)
**Text Template**: `"Someone from {city} just purchased {product} for ${price}"`

**Variables**:
- `city`: `New York|Los Angeles|Chicago|Miami|Seattle`
- `product`: `Premium Widget|Super Gadget|Amazing Tool|Best Product`
- `price`: `29|49|79|99`

**Result**: "Someone from Miami just purchased Super Gadget for $49"

### Social Proof
**Text Template**: `"{buyer} from {city} just left a {rating}-star review!"`

**Variables**:
- `buyer`: `Sarah|Mike|Emma|John|Lisa`
- `city`: `New York|Los Angeles|Chicago`
- `rating`: `4|5`

**Result**: "Mike from Chicago just left a 5-star review!"

### Time-based Notifications
**Text Template**: `"Ordered {time} minutes ago"`

**Variables**:
- `time`: `5-15`

**Result**: "Ordered 12 minutes ago"

---

## WooCommerce Product Notification Template

When you select the **WooCommerce Product Notification** template, the plugin will use your WooCommerce products to populate notification variables. You can also use custom variables alongside WooCommerce variables.

### Built-in WooCommerce Variables
- `{product}`: Product name
- `{price}`: Product price
- `{image}`: Product image (used as the popup image)

### Example Usage

**Text Template:**
```
{buyer} from {city} just bought *{product}* for ${price}!
```

**Custom Variables:**
- `buyer`: `Sarah|Mike|Emma|John|Lisa`
- `city`: `New York|Los Angeles|Chicago|Miami|Seattle`

**Result:**
> "Emma from Miami just bought Awesome T-shirt for $29!"

- `{product}` and `{price}` are filled from a random WooCommerce product.
- `{buyer}` and `{city}` are filled from your custom variables.
- `{image}` will show the product image in the popup. If not available, a default icon is shown.

### How to Use
1. In the plugin settings, select **WooCommerce Product Notification** as the template.
2. Enter your desired text template using any combination of WooCommerce and custom variables.
3. Add any custom variables you want (e.g., `buyer`, `city`).
4. Save settings. The popup will now use both WooCommerce and custom variables.

---

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
- **WooCommerce Integration**: Fetches product data and images for notifications
- **Text Formatting**: Supports *bold* and _italic_ formatting
- **Popup Position & Effect**: Supports left/right position and fade/slide effects
- **Responsive Design**: CSS Grid and Flexbox for responsive layout

### Admin Interface

- **WordPress Settings API**: Uses WordPress native settings framework
- **Dynamic Forms**: JavaScript-powered dynamic form fields for variables
- **Data Validation**: Server-side validation for all settings
- **Type-specific UI**: Different input fields based on variable type
- **Template System**: Easily extensible for more notification templates
- **Popup Position & Effect**: Easily extensible for more effects and positions

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

---

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
- WooCommerce (for WooCommerce template)

## Support

For support and feature requests, please create an issue in the plugin repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.1
- Added text formatting: `*bold*` and `_italic_` supported in notification templates
- Improved WooCommerce template: custom variables can be used alongside product variables
- Added (documented) support for popup position (left/right) and effect (fade/slide)

### Version 1.0.0
- Initial release
- Multipurpose notification system
- Flexible variable support (array, text, number, range)
- Curly bracket variable processing
- Admin settings interface
- Responsive design
- WooCommerce product notification template 