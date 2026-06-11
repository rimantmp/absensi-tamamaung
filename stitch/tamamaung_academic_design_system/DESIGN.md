---
name: Tamamaung Academic Design System
colors:
  surface: '#fef9ef'
  surface-dim: '#dedad0'
  surface-bright: '#fef9ef'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f8f3e9'
  surface-container: '#f2ede4'
  surface-container-high: '#ece8de'
  surface-container-highest: '#e7e2d8'
  on-surface: '#1d1c16'
  on-surface-variant: '#444748'
  inverse-surface: '#32302a'
  inverse-on-surface: '#f5f0e6'
  outline: '#747878'
  outline-variant: '#c4c7c7'
  surface-tint: '#5f5e5e'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#1c1b1b'
  on-primary-container: '#858383'
  inverse-primary: '#c9c6c5'
  secondary: '#615e55'
  on-secondary: '#ffffff'
  secondary-container: '#e4dfd3'
  on-secondary-container: '#656359'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#251a00'
  on-tertiary-container: '#a67e0b'
  error: '#ef4444'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e5e2e1'
  primary-fixed-dim: '#c9c6c5'
  on-primary-fixed: '#1c1b1b'
  on-primary-fixed-variant: '#474646'
  secondary-fixed: '#e7e2d6'
  secondary-fixed-dim: '#cac6ba'
  on-secondary-fixed: '#1d1c14'
  on-secondary-fixed-variant: '#49473e'
  tertiary-fixed: '#ffdf9d'
  tertiary-fixed-dim: '#f0c050'
  on-tertiary-fixed: '#251a00'
  on-tertiary-fixed-variant: '#5b4300'
  background: '#fef9ef'
  on-background: '#1d1c16'
  surface-variant: '#e7e2d8'
  brand-pink: '#ff4d8b'
  brand-teal: '#1a3a3a'
  brand-lavender: '#b8a4ed'
  brand-peach: '#ffb084'
  brand-ochre: '#e8b94a'
  ink: '#0a0a0a'
  body: '#3a3a3a'
  muted: '#6a6a6a'
  hairline: '#e5e5e5'
  success: '#22c55e'
typography:
  display-xl:
    fontFamily: Inter
    fontSize: 72px
    fontWeight: '500'
    lineHeight: '1.0'
    letterSpacing: -2.5px
  display-lg:
    fontFamily: Inter
    fontSize: 56px
    fontWeight: '500'
    lineHeight: '1.05'
    letterSpacing: -2px
  display-md:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '500'
    lineHeight: '1.1'
    letterSpacing: -1px
  display-md-mobile:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '500'
    lineHeight: '1.1'
    letterSpacing: -0.5px
  title-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: -0.3px
  title-md:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: '1.4'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.55'
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.55'
  button:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.0'
  nav-link:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xxs: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  xxl: 48px
  section: 96px
  sidebar-width: 260px
---

## Brand & Style

The design system for SD Inpres Tamamaung IV bridges the gap between a friendly primary school environment and a modern, efficient administrative tool. It adopts a **Playful B2B SaaS** aesthetic, drawing inspiration from high-end productivity tools to elevate a local utility into a professional experience.

The visual style is **Corporate / Modern** with a **Tactile** twist. It uses a warm, parchment-like canvas to reduce eye strain during long administrative sessions, paired with "Claymation" inspired elements—specifically high-saturation accent cards and progressive corner rounding—to maintain a youthful, approachable energy for students and teachers.

The interface prioritizes clarity and "visual voltage." High-contrast primary actions use deep blacks against the cream canvas, while secondary information is categorized through a vibrant 6-color palette. This ensures that the attendance system feels less like a rigid government database and more like an inviting digital companion for the school community.

## Colors

The color palette is anchored by a warm neutral base (`--canvas`) that provides a soft, non-clinical background for the entire application. 

### Key Color Roles
- **Primary**: Deep black/navy is reserved for critical path actions (CTAs), primary navigation text, and headlines.
- **Secondary**: A slightly darker cream used for sidebar surfaces and footer bands to create subtle depth without introducing harsh shadows.
- **Tertiary/Accents**: A saturated 6-color palette (Pink, Teal, Lavender, Peach, Ochre) is used exclusively for feature cards, status groupings, and student category badges. 
- **Status**: Standard semantic colors (Green/Red) are utilized for immediate scan feedback, ensuring accessibility during the high-speed attendance process.

Avoid using pure white (#FFFFFF) for large surfaces; it should only be used for text inside dark primary buttons or specific high-contrast card elements.

## Typography

This design system utilizes **Inter** for all typographic roles to ensure maximum legibility and systematic consistency across the administrative dashboard and scanning kiosk. 

### Display Rules
Headlines (Display XL through MD) use a medium weight (500) and **negative letter-spacing** to mimic a tight, editorial look. This creates the "Plain Black" aesthetic mentioned in the brand requirements, giving the system a distinct, high-end feel.

### Functional Roles
- **Body Text**: Uses a generous 1.55 line height for comfortable reading of student lists and reports.
- **Data/Identifiers**: NIS/NISN numbers should use `body-md` with a slightly tighter letter spacing for quick recognition.
- **Mobile Considerations**: For the public scanning interface, `display-md-mobile` is the primary level for showing the student's name upon a successful scan.

## Layout & Spacing

The system follows a strict **4px base rhythm**. Layouts are divided into two distinct shells:

1.  **Internal App Shell (Sidebar-based)**:
    - **Sidebar**: A fixed 260px width left-hand navigation (`--surface-soft`).
    - **Content Area**: A fluid container with `xl` (32px) padding that stretches to fill the screen, accommodating complex data tables and reporting grids.
2.  **Public Shell (Self-Attendance Kiosk)**:
    - **Centered Layout**: A minimal, no-grid focus on the central scanning area. It uses `section` (96px) vertical margins to center the camera interface on large screens.

### Grid & Breakpoints
- **Desktop**: 12-column fluid grid with 16px gutters for dashboard widgets.
- **Tablet**: Sidebar transitions to a collapsed icon-only state or a hidden drawer.
- **Mobile**: Single-column vertical flow with reduced padding (`md`) to maximize screen real estate for the barcode scanner.

## Elevation & Depth

This design system avoids traditional shadows in favor of **Tonal Layering** and high-contrast borders.

- **Surface Tiers**: Depth is indicated by color shifts. The main page background is `--canvas` (#fffaf0), while secondary containers (like the Sidebar or Dashboard cards) use `--surface-soft` or `--surface-card`.
- **Low-Contrast Outlines**: Instead of shadows, use `1px` borders in `--hairline` (#e5e5e5) for input fields, secondary cards, and table dividers.
- **Active State Elevation**: Interactive elements do not "lift" via shadows; instead, they change fill color (e.g., from `--primary` to `--primary-active`) or introduce a subtle 1px border.
- **Z-Index Layering**: The Barcode Scanner overlay and success/error toasts are the only elements that sit "above" the UI, utilizing a slight backdrop blur to maintain focus.

## Shapes

The shape language is **Progressively Rounded**, meaning larger components receive more aggressive corner treatment to emphasize the "Clay" aesthetic.

- **Standard UI (Buttons, Inputs)**: 0.5rem (8px) for a modern, approachable feel.
- **Information Containers (Cards, Testimonials)**: 1rem (16px) for distinct separation.
- **Feature/Saturated Cards**: 1.5rem (24px) to emphasize their decorative and high-energy role.
- **Pills**: Navigation tabs and status badges (Hadir, Sakit, Alpa) always use the `pill-shaped` (9999px) rounding for immediate recognition as status indicators.

Borders for printed assets (Student Barcode Cards) should remain relatively sharp (xs) to ensure no information is lost during the printing and cutting process.

## Components

### Buttons
- **Primary**: Solid `--primary` fill, `--on-primary` text, `md` rounding. No shadow.
- **Secondary/Ghost**: No fill, 1px `--hairline` border, `--primary` text.
- **Status Buttons**: Used in the public scanner for manual input; large, touch-friendly targets using status-specific colors.

### The Barcode Card
The most critical component. It must be styled for both screen and print:
- **Header**: Student name in `title-md`.
- **Center**: High-contrast black/white barcode area.
- **Footer**: NIS/NISN and Class ID in `body-sm`.
- **Border**: A clean 1px `--hairline` to act as a cutting guide.

### Input Fields
- **Search/Forms**: `--canvas` fill with a 1px `--hairline` border. On focus, the border changes to `--primary` with a subtle 2px inset ring.

### Feature Cards (Dashboard)
- Used for quick stats (e.g., "Total Students," "Present Today").
- Each card should cycle through the saturated palette: Pink, Teal, Lavender, Peach, Ochre.
- Internal padding should be `xl` (32px) to allow the `title-lg` headers to breathe.

### Sidebar Nav Items
- **Inactive**: `nav-link` text in `--muted`.
- **Active**: `--surface-card` background with `md` rounding and `--ink` text weight set to 600.