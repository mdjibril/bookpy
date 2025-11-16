# bookpy — Development Plan

## Project Overview
A PHP web application for booking appointments with a public booking form, admin panel, and email notifications.

**Tech Stack:** PHP 8.3 | MySQL | HTML/CSS/JS | Resend API

---

## Phase 1: Project Setup ✅
- [x] Initialize composer.json with PSR-4 autoload
- [x] Create project directory structure
- [x] Set up .env configuration
- [x] Create Router utility for request dispatch
- [x] Build public front controller (public/index.php)

## Phase 2: Public Booking Page ✅
- [x] Create public homepage (calendar placeholder)
- [x] Build booking form UI (name, email, phone, date, time)
- [x] Wire POST /booking route to BookingController
- [x] Implement basic form validation
- [x] Test booking submission in dev

## Phase 3: Email System ✅
- [x] Create EmailService with terminal/resend/smtp modes
- [x] Send booking acknowledgement email (terminal mode)
- [x] Configure MAIL_MODE env variable
- [x] Test email output in dev server

## Phase 4: Database Setup ✅
- [x] Install php-mysql driver
- [x] Create MySQL bookings table via migration
- [x] Wire BookingRepository to persist bookings
- [x] Test booking persistence to DB

## Phase 5: Admin Panel ✅
- [x] Create admin dashboard route (/admin)
- [x] Build AdminController with dashboard() method
- [x] Display pending bookings in a table
- [x] Add "Confirm" button for each booking
- [x] Implement confirmBooking() action
- [x] Send confirmation email with PDF attachment

## Phase 6: Email Templates & PDFs ✅
- [x] Create editable email templates in admin (CRUD complete)
- [x] Build PDF generation for appointment details
- [x] Attach PDF to confirmation emails
- [x] Use database templates for sending dynamic emails
- [x] Store template versions in DB

## Phase 7: Mobile Responsive Design ✅
- [x] Create mobile-adaptive CSS for public and admin areas
- [x] Test responsive layout on devices
- [x] Ensure mobile form usability

## Phase 8: Admin Authentication ✅
- [x] Create login form
- [x] Implement session-based auth
- [x] Protect /admin/* routes with auth middleware
- [x] Add logout functionality

## Phase 9: Advanced Features ✅
- [x] Time slot availability system
- [x] Calendar picker UI (replace placeholder)
- [x] Admin email notification on new booking
- [x] Booking cancellation flow
- [x] Status filters (pending, confirmed, cancelled)

## Phase 10: Deployment (TODO)
- [ ] Create deployment guide for Netlify/Vercel (frontend)
- [ ] Set up backend hosting (Render, Fly.io, etc.)
- [ ] Configure environment variables on host
- [ ] Test production email (Resend API)
- [ ] Deploy and verify

---

## Current Status
**Phase:** 10 (Deployment)

**Last Updated:** November 14, 2025

**Notes:**
- The entire application is now mobile-responsive.
- All core and advanced features are complete, including admin notifications and a user cancellation flow.
- The application is feature-complete and ready for the final deployment phase.