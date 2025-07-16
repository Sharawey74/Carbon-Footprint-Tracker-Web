# 🌱 Carbon Footprint Tracker for Coffee Operations

A comprehensive web-based system for monitoring, calculating, and reporting carbon emissions across multiple stages of coffee production, packaging, and distribution operations. The system manages 16 branches spread across 4 Egyptian cities, providing real-time carbon footprint tracking and environmental impact analysis.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1.svg?style=flat&logo=mysql)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E.svg?style=flat&logo=javascript)
![CSS3](https://img.shields.io/badge/CSS3-1572B6.svg?style=flat&logo=css3)

## 🌟 Overview

The Carbon Footprint Tracker is designed specifically for coffee industry operations, providing detailed emission calculations and environmental impact assessments across the entire coffee supply chain - from production to distribution. The system serves multiple Egyptian cities including Alexandria, Aswan, Cairo, and Suez.

## ✨ Key Features

### 🔢 Carbon Emission Calculations
- **Production Emissions**: Track emissions from coffee bean processing and production
- **Packaging Emissions**: Monitor packaging waste and associated carbon footprint (6 kg CO2 per kg waste)
- **Distribution Emissions**: Calculate vehicle emissions based on fleet type and distance (2.68 kg CO2 per liter fuel)
- **Real-time Metrics**: Live calculation and aggregation of emission data

### 👥 Role-Based Access Control
- **Branch User**: Data entry and branch-level operations
- **Operations Manager**: Multi-branch oversight and coordination
- **CIO**: System-wide analytics and technical reports
- **CEO**: Executive dashboard with high-level insights and strategic overview

### 📊 Advanced Analytics & Reporting
- **Visual Dashboards**: Interactive charts and graphs using modern web technologies
- **PDF Report Generation**: Comprehensive branch and city-level reports
- **CSV Import/Export**: External data integration capabilities
- **Emissions Trending**: Historical data analysis and forecasting

### 🔔 Smart Monitoring
- **Threshold Alerts**: Pop-up notifications for emissions breaches
- **Audit Logging**: Complete system activity tracking
- **User Activity Monitoring**: Detailed logs of all system changes
- **Real-time Notifications**: Instant alerts for critical events

### 🌍 Multi-City Operations
- **Alexandria Branch Operations**
- **Aswan Regional Management**
- **Cairo Central Hub**
- **Suez Distribution Center**

## 🏗️ System Architecture

### Technology Stack
- **Backend**: PHP 7.4+ with object-oriented design
- **Database**: MySQL 5.7+ with optimized schema
- **Frontend**: Modern JavaScript (ES6+), CSS3, Bootstrap
- **Reporting**: PDF generation with TCPDF
- **Charts**: Interactive data visualization

### Architecture Patterns
- **MVC Pattern**: Clear separation of concerns
- **DAO Pattern**: Data access abstraction layer
- **Service Layer**: Business logic encapsulation
- **Dependency Injection**: Modular and testable design

### Core Components

```
src/
├── config/
│   ├── config.php          # Application configuration
│   ├── database.php        # Database connection
│   ├── init.php           # System initialization
│   └── container.php      # Dependency injection setup
├── controllers/
│   ├── auth.php           # Authentication controller
│   ├── branch_data_entry.php # Branch operations controller
│   ├── op_manager_dashboard.php # Operations management
│   └── report_controller.php # Report generation
├── models/
│   ├── Branch.php         # Branch entity model
│   ├── CarbonFootprintMetrics.php # Metrics calculation
│   ├── CoffeeProduction.php # Production data model
│   ├── CoffeePackaging.php # Packaging data model
│   └── CoffeeDistribution.php # Distribution data model
├── services/
│   ├── CarbonFootprintService.php # Core business logic
│   ├── BranchService.php  # Branch management
│   ├── UserService.php    # User management
│   └── ReportGenerationService.php # Report creation
├── dao/
│   ├── interfaces/        # Data access interfaces
│   └── impl/             # Database implementations
├── utils/
│   ├── EmissionCalculator.php # Emission formulas
│   └── DependencyContainer.php # IoC container
└── public/
    ├── css/styles.css     # Application styling
    ├── js/main.js        # Frontend interactions
    └── index.php         # Application entry point
```

## 🚀 Installation & Setup

### Prerequisites
- **PHP 7.4 or higher** with extensions:
  - PDO MySQL
  - JSON
  - Session
  - DateTime
- **MySQL 5.7 or higher**
- **Web Server** (Apache/Nginx)
- **Modern Web Browser** (Chrome, Firefox, Safari, Edge)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Sharawey74/Carbon-Footprint-Tracker-Web.git
   cd Carbon-Footprint-Tracker-Web
   ```

2. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u your_username -p your_database < database/schema.sql
   ```

3. **Configuration**
   ```php
   // config/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'carbon_tracker');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **Web Server Configuration**
   ```apache
   # Apache Virtual Host Example
   <VirtualHost *:80>
       DocumentRoot "/path/to/Carbon-Footprint-Tracker-Web/public"
       ServerName carbontracker.local
       DirectoryIndex index.php
   </VirtualHost>
   ```

5. **Access the Application**
   ```
   http://localhost/Carbon-Footprint-Tracker-Web
   ```

## 📋 Usage Guide

### User Authentication
The system automatically redirects to the CEO dashboard with pre-configured access:
- **Username**: Osama Hanafy (CEO)
- **Role**: Executive access with full system visibility

### Branch Data Entry
Branch users can input data for three main categories:

1. **Production Data**
   - Supplier information
   - Coffee type (Arabica, Robusta)
   - Product type (Ground, Whole Bean)
   - Quantity produced (kg)

2. **Packaging Data**
   - Packaging waste generated (kg)
   - Automatic emission calculation (6 kg CO2 per kg waste)

3. **Distribution Data**
   - Vehicle type (Minivan: 10 km/L, Pickup Truck: 15 km/L)
   - Number of vehicles
   - Distance per vehicle (km)
   - Automatic emission calculation (2.68 kg CO2 per liter fuel)

### Dashboard Features

#### Executive Dashboard (CEO)
- Total emission metrics across all branches
- City-wise emission breakdown
- Branch performance comparison
- Reduction strategy overview

#### Operations Dashboard (OP Manager)
- Branch management and coordination
- Cross-branch analytics
- Operational efficiency metrics
- Resource allocation insights

#### Branch Dashboard (Branch User)
- Local emission tracking
- Data entry forms
- Branch-specific metrics
- Progress monitoring

## 📊 Emission Calculation Formulas

### Production Emissions
```php
// Production emissions calculation
$emissions = EmissionCalculator::calculateProductionEmissions($quantity, $coffeeType);
```

### Packaging Emissions
```php
// Packaging waste emissions: 6 kg CO2 per kg waste
$emissions = $packagingWaste * 6.0;
```

### Distribution Emissions
```php
// Vehicle emissions: 2.68 kg CO2 per liter fuel consumed
$fuelConsumed = $totalDistance / $fuelEfficiency;
$emissions = $fuelConsumed * 2.68;
```

### Vehicle Fuel Efficiency
- **Minivan**: 10 km/L
- **Pickup Truck**: 15 km/L

## 🌍 Multi-Language Support

The system supports both English and Arabic languages:

```php
// Language files
i18n/en.php    // English translations
i18n/ar.php    // Arabic translations (العربية)
```

Key features available in Arabic:
- Complete UI translation
- Right-to-left text support
- Localized date/time formats
- Currency and number formatting

## 🔧 API Endpoints

### Data Entry APIs
```php
POST /controllers/branch_data_entry.php?action=addProduction
POST /controllers/branch_data_entry.php?action=addPackaging
POST /controllers/branch_data_entry.php?action=addDistribution
```

### Reporting APIs
```php
GET /controllers/report_controller.php?action=download&branch_id={id}
GET /controllers/report_controller.php?action=view&branch_id={id}
```

### Dashboard APIs
```php
GET /controllers/op_manager_dashboard.php?action=getBranchMetrics
GET /controllers/op_manager_dashboard.php?action=getCityMetrics
```

## 📈 Database Schema

### Core Tables
- **City**: City information and regional data
- **Branch**: Branch locations and employee counts
- **User**: User accounts with role-based permissions
- **CoffeeProduction**: Production data and emissions
- **CoffeePackaging**: Packaging waste and emissions
- **CoffeeDistribution**: Vehicle usage and emissions

### Supporting Tables
- **AuditLogging**: System activity tracking
- **Notification**: User notifications and alerts
- **ReductionStrategy**: Environmental improvement plans
- **PlanStatus**: Strategy implementation tracking

## 🔒 Security Features

- **Role-based Access Control**: Four distinct user roles
- **Password Security**: Salted and hashed passwords
- **Session Management**: Secure session handling
- **SQL Injection Prevention**: Prepared statements
- **Input Validation**: Comprehensive data validation
- **Audit Trail**: Complete activity logging

## 🚀 Performance Optimizations

- **Database Indexing**: Optimized query performance
- **Caching Strategy**: Session-based data caching
- **Lazy Loading**: On-demand data retrieval
- **Compressed Assets**: Minimized CSS/JS files
- **Connection Pooling**: Efficient database connections

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-4 autoloading standards
- Implement comprehensive error handling
- Add unit tests for new features
- Document all public methods
- Follow the existing code style

## 📞 Support

For technical support or questions:
- **Repository**: [Carbon-Footprint-Tracker-Web](https://github.com/Sharawey74/Carbon-Footprint-Tracker-Web)
- **Issues**: Use GitHub Issues for bug reports
- **Documentation**: Refer to inline code documentation

## 📄 License

This project is proprietary and confidential. All rights reserved.

## 🌟 Acknowledgments

- Coffee industry environmental standards
- Egyptian environmental regulations
- Carbon footprint calculation methodologies
- Bootstrap and modern web technologies

---

**© 2024 Carbon Footprint Tracker** - Sustainable Coffee Operations Management

