# Carbon Footprint Tracker for Coffee Operations

A web-based system for monitoring, calculating, and reporting carbon emissions across multiple stages of coffee production, packaging, and distribution for 16 branches spread across 4 cities.

## Features

- Carbon emission calculations based on formulas for production, packaging, and distribution
- Role-based access control with four distinct roles (Branch User, OP Manager, CIO, CEO)
- Data tracking for all branch operations and vehicle usage
- Reduction plan creation, status tracking, and profitability estimation
- Audit logging of all system changes
- Pop-up notifications for emissions threshold breaches
- Visual and PDF reporting tools
- CSV import capability for external data integration

## Installation

1. Clone the repository to your web server directory
2. Import the database schema from `database/schema.sql`
3. Configure your database connection in `config/config.php`
4. Ensure your web server has PHP 7.4+ and MySQL 5.7+
5. Access the application at `http://localhost/CarbonTracker-Web`

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## User Roles

1. **Branch User**: Responsible for data entry at branch level
2. **OP Manager**: Oversees operations across branches
3. **CIO**: Access to analytics and system-wide reports
4. **CEO**: Executive dashboard with high-level insights

## License

This project is proprietary and confidential.
