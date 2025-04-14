# Portfolio Showcase WordPress Plugin

A modern and responsive WordPress plugin for showcasing portfolio projects with beautiful carousels and color palettes.

## Features

- Custom Post Type for portfolio projects
- Modern and responsive image carousel
  - Smooth transitions and animations
  - Fullscreen mode
  - Touch-enabled for mobile devices
  - Thumbnails navigation
  - Customizable image titles and descriptions
  - Configurable text positions
- Interactive color palette
  - Flexible layout
  - Color tooltips
  - Customizable height
  - Optional comments for each color
- Customizable styling options
  - Padding and margin controls
  - Custom CSS support
  - Responsive design

## Requirements

- WordPress 6.7.2 or higher
- PHP 7.4 or higher
- Modern web browser with JavaScript enabled

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now"
5. After installation, click "Activate"

## Usage

### Creating a Portfolio Project

1. In the WordPress admin panel, go to "Portfolio"
2. Click "Add New"
3. Enter a title for your project
4. Add a description in the main content area
5. Configure the carousel:
   - Click "Add Images" to upload or select images
   - Set titles and descriptions for each image
   - Choose a main image
   - Configure display options
6. Set up the color palette:
   - Add colors using the color picker
   - Add optional comments for each color
   - Adjust the height and position of comments
7. Customize the styling:
   - Set padding and margins
   - Add custom CSS if needed
8. Publish your project

### Displaying Projects

Use the shortcode `[portfolio_showcase id="project_id"]` to display a specific project.

Options:
- `id`: The ID of the project to display (required)

Example:
```
[portfolio_showcase id="123"]
```

### Customization

The plugin provides several customization options:

1. Carousel Settings:
   - Enable/disable fullscreen mode
   - Set description position (top/bottom)
   - Set title position (top-left, top-right, bottom-left, bottom-right, center)
   - Configure fullscreen background color

2. Color Palette Settings:
   - Adjust rectangle height
   - Set comment position (top/bottom)

3. Style Options:
   - Set custom padding
   - Set custom margins
   - Add custom CSS

## Support

For support, feature requests, or bug reports, please create an issue in the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Built with WordPress
- Uses jQuery for enhanced functionality
- Inspired by modern portfolio designs 