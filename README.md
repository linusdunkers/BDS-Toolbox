# BDS Toolbox

**BDS Toolbox** is a WordPress plugin designed to promote awareness and support for the BDS (Boycott, Divestment, Sanctions) movement. The plugin filters visitors based on GeoIP location and provides tools to share information about the movement, helping site owners advocate for peace and human rights.

## Features
- **GeoIP Filtering**: Detect visitors' geographical location and customize their experience accordingly.
- **Custom Content**: Display default or custom HTML content for visitors from specific regions.
- **User-Friendly Interface**: Easily configure settings via the WordPress admin dashboard.
- **Lightweight and Efficient**: Optimized for performance without impacting site speed.
- **Security Best Practices**: Uses WordPress nonces, sanitization, and escaping functions for safe operation.

## Installation

### 📌 **Recommended Method (WordPress Plugin Directory)**
1. Navigate to **Plugins > Add New** in your WordPress admin panel.
2. Search for **BDS Toolbox**.
3. Click **Install Now**, then **Activate** the plugin.
4. Go to **Settings > BDS Toolbox** to configure your preferences.

### 🛠 **Manual Installation (Alternative)**
1. **Download the Plugin**:  
   - Get the latest release from the [WordPress Plugin Directory](https://wordpress.org/plugins/bds-toolbox/) or this GitHub repository.  
   - Extract and upload the `bds-toolbox` folder to your `/wp-content/plugins/` directory.
2. **Activate the Plugin**:  
   - In the WordPress admin dashboard, go to **Plugins** and activate **BDS Toolbox**.
3. **Configure Settings**:  
   - Navigate to **Settings > BDS Toolbox** to adjust the content shown to visitors.

## How It Works
The plugin uses the **GeoLite2 Country** database to determine the visitor's location. Visitors from specific countries (e.g., Israel) are shown tailored content, such as messages advocating for the BDS movement or promoting human rights campaigns.

### ⚠️ **GeoIP Database Requirement**
- The plugin includes **GeoLite2-Country.mmdb**, but if a newer version is needed, you can download an updated database from [MaxMind](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data).
- Ensure the `.mmdb` file is correctly placed in the plugin directory for location detection to work.

## Contributing
We welcome contributions to enhance the plugin's functionality and impact. To contribute:
- Fork this repository.
- Create a new branch for your feature or bug fix.
- Submit a pull request with detailed information about your changes.

## License
This plugin is licensed under the GNU General Public License v2.0 or later. See the [LICENSE](LICENSE) file for details.

## Learn More
- **BDS Movement**: [Visit the official BDS website](https://bdsmovement.net/)
- **Plugin Documentation**: [WordPress Plugin Page](https://wordpress.org/plugins/bds-toolbox/)
- **GitHub Repository**: [View on GitHub](https://github.com/linusdunkers/BDS-Toolbox)
