#!/bin/bash

# 89-CATALOG SCHEDULER - Complete Scheduler Setup & Management Script
# This script helps you set up and manage all scheduled tasks for 89-Catalog

echo "🎮 89-CATALOG SCHEDULER SETUP & MANAGEMENT"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Error: docker-compose.yml not found. Run this script from your project root."
    exit 1
fi

echo ""
echo "✅ Found docker-compose.yml - proceeding with setup..."

# Display all scheduled tasks
echo ""
echo "📋 SCHEDULED TASKS:"
echo ""
echo "1️⃣ ZONA PROMAX HUB Data Updates"
echo "   Command: zona:update-data"
echo "   Frequency: Every 30 seconds"
echo "   Description: Updates ZONA PROMAX HUB live data and broadcasts changes"
echo ""
echo "2️⃣ Random Data Updates"
echo "   Command: data:update-random"
echo "   Frequency: Every 30 minutes"
echo "   Description: Auto-updates RTP games, tools, and hot_and_fresh with random values"
echo ""

# Option 1: Docker Scheduler Service (Already configured)
echo ""
echo "📋 SCHEDULER OPTIONS:"
echo ""
echo "1️⃣ DOCKER SCHEDULER SERVICE (Automatic - Recommended):"
echo "   ✅ Already configured in docker-compose.yml"
echo "   ✅ Runs automatically when you start containers"
echo "   ✅ No host system configuration needed"
echo ""
echo "   TO START: docker-compose up -d scheduler"
echo "   TO MONITOR: docker-compose logs -f scheduler"
echo "   TO VIEW LOGS: docker logs -f 89-catalog-scheduler"
echo ""

# Option 2: Host System Cron
echo "2️⃣ HOST SYSTEM CRON (Alternative):"
echo "   • Uses your server's cron instead of Docker"
echo "   • Good if you prefer host-level scheduling"
echo ""
echo "   TO SET UP: Add this to your crontab (crontab -e):"
echo "   * * * * * cd $(pwd) && docker-compose exec -T app php artisan schedule:run >> /dev/null 2>&1"
echo ""

# Test current setup
echo "🧪 TESTING CURRENT SETUP:"
echo ""

# Check if containers are running
echo "Checking Docker containers..."
if docker-compose ps | grep -q "Up"; then
    echo "✅ Docker containers are running"

    # Test scheduler service
    if docker-compose ps | grep -q "89-catalog-scheduler"; then
        echo "✅ Scheduler service is running"
        echo ""
        echo "📊 SCHEDULER STATUS:"
        docker-compose logs scheduler | tail -10
    else
        echo "⚠️  Scheduler service not running (start with: docker-compose up -d scheduler)"
    fi

    # Test ZONA command
    echo ""
    echo "🧪 TESTING ZONA COMMAND:"
    docker-compose exec -T app php artisan zona:update-data 2>/dev/null | head -5

    # Test Random Data command
    echo ""
    echo "🧪 TESTING RANDOM DATA COMMAND:"
    docker-compose exec -T app php artisan data:update-random 2>/dev/null | head -5

else
    echo "⚠️  Docker containers not running"
    echo "   Start them with: docker-compose up -d"
fi

echo ""
echo "🎯 QUICK START COMMANDS:"
echo ""
echo "📦 CONTAINER MANAGEMENT:"
echo "   1. Start scheduler:        docker-compose up -d scheduler"
echo "   2. Stop scheduler:         docker-compose stop scheduler"
echo "   3. Restart scheduler:     docker-compose restart scheduler"
echo "   4. Check status:          docker-compose ps scheduler"
echo ""
echo "📊 LOG VIEWING:"
echo "   5. Monitor scheduler:     docker-compose logs -f scheduler"
echo "   6. View last 100 lines:   docker logs --tail 100 89-catalog-scheduler"
echo "   7. View with timestamps:   docker logs -f --timestamps 89-catalog-scheduler"
echo "   8. View last hour:        docker logs --since 1h 89-catalog-scheduler"
echo ""
echo "🧪 MANUAL TESTING:"
echo "   9. Test ZONA update:       docker-compose exec app php artisan zona:update-data"
echo "  10. Test random data:       docker-compose exec app php artisan data:update-random"
echo "  11. Force ZONA update:     docker-compose exec app php artisan zona:update-data --force"
echo "  12. Force random update:   docker-compose exec app php artisan data:update-random --force"
echo ""
echo "📈 MONITORING:"
echo "  13. View all logs:         docker-compose logs -f"
echo "  14. Check schedule list:    docker-compose exec app php artisan schedule:list"
echo "  15. Run schedule now:      docker-compose exec app php artisan schedule:run"
echo ""
echo "✅ Your schedulers will run automatically:"
echo "   • ZONA PROMAX HUB updates every 30 seconds"
echo "   • Random data updates every 30 minutes"
echo ""
echo "📖 For more info, see: ZONA_SCHEDULING_README.md"
