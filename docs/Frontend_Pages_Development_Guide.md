# Frontend Pages Development Document for DTT by Logix

This document outlines the requirements and specifications for the frontend pages to be developed for the DTT by Logix application. The pages include Material Types, Profile, Reports, and Dashboard.

---

## 1. Dashboard Page

### Purpose
The Dashboard is the root landing page after user login, providing an overview and quick access to key features.

### Requirements
- **Route:** `/` (root path)
- **Permissions:** Requires `view.dashboard` permission
- **Component:** Use the existing `Dashboard.vue` component as a base
- **API Endpoints:** 
  - `/api/dashboard/stats` - for summary statistics
  - `/api/projects/count` - project counts
  - `/api/clients/count` - client counts
  - `/api/material-types/count` - material types counts

### Features
- Display summary statistics (e.g., projects, clients, material types counts)
- Quick links to Projects, Clients, Material Types, Reports
- Conditional UI elements based on permissions (e.g., create/edit/delete project buttons visible only to authorized users)
- Recent activity feed
- Quick action buttons for common tasks

### Technical Implementation
```vue
<template>
  <div class="dashboard-container">
    <CanAccess permission="view.dashboard">
      <!-- Dashboard content -->
    </CanAccess>
  </div>
</template>

<script setup>
import { CanAccess } from '@/components'
import { usePermissions } from '@/composables/usePermissions'

const { hasPermission } = usePermissions()
</script>
```

---

## 2. Material Types Page

### Purpose
Manage material types used in the system.

### Requirements
- **Route:** `/material-types`
- **Permissions:** Requires `view.material-types` permission
- **Component:** Create new `MaterialTypes.vue` component
- **API Endpoints:**
  - `GET /api/material-types` - list material types
  - `POST /api/material-types` - create (admin only)
  - `PUT /api/material-types/{id}` - update (admin only)
  - `DELETE /api/material-types/{id}` - delete (admin only)

### Features
- List all material types with pagination, sorting, and filtering
- Admin-only actions: create, edit, delete material types
- Display categories and units of measure
- Search functionality
- Bulk operations (admin only)

### UI Components
- Data table with sorting and filtering
- Modal forms for create/edit operations
- Confirmation dialogs for delete operations
- Loading states and error handling
- Responsive design for mobile devices

### Technical Implementation
```vue
<template>
  <div class="material-types-page">
    <div class="page-header">
      <h1>Material Types</h1>
      <CanAccess permission="create.material-types">
        <button @click="openCreateModal" class="btn-primary">
          Add Material Type
        </button>
      </CanAccess>
    </div>
    
    <div class="material-types-table">
      <!-- Table implementation -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { CanAccess } from '@/components'
import { useMaterialTypes } from '@/composables/useMaterialTypes'

const { materialTypes, loading, fetchMaterialTypes } = useMaterialTypes()

onMounted(() => {
  fetchMaterialTypes()
})
</script>
```

---

## 3. Reports Page

### Purpose
Display various reports and analytics for projects, clients, and material usage.

### Requirements
- **Route:** `/reports`
- **Permissions:** Requires `view.reports` permission
- **Component:** Create new `Reports.vue` component
- **API Endpoints:**
  - `GET /api/reports/projects` - project reports
  - `GET /api/reports/clients` - client reports
  - `GET /api/reports/materials` - material usage reports
  - `GET /api/reports/export/{type}` - export reports (admin only)

### Features
- Display available reports with options to filter and export
- Advanced reports accessible only to admins
- Date range filtering
- Export functionality (PDF, Excel, CSV)
- Interactive charts and graphs
- Real-time data updates

### UI Components
- Report dashboard with cards/tiles
- Date range picker
- Filter controls
- Chart components (using Chart.js or similar)
- Export buttons with format options
- Loading states and error handling

### Technical Implementation
```vue
<template>
  <div class="reports-page">
    <div class="page-header">
      <h1>Reports & Analytics</h1>
      <div class="report-filters">
        <DateRangePicker v-model="dateRange" />
        <CanAccess permission="export.reports">
          <ExportButton @export="handleExport" />
        </CanAccess>
      </div>
    </div>
    
    <div class="reports-grid">
      <ReportCard 
        v-for="report in availableReports" 
        :key="report.id"
        :report="report"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { CanAccess, DateRangePicker, ExportButton, ReportCard } from '@/components'
import { useReports } from '@/composables/useReports'

const { reports, loading, fetchReports } = useReports()
const dateRange = ref({ start: null, end: null })

onMounted(() => {
  fetchReports()
})
</script>
```

---

## 4. Profile Page

### Purpose
Allow users to view and edit their profile information and account settings.

### Requirements
- **Route:** `/profile`
- **Permissions:** Requires user to be authenticated
- **Component:** Create new `Profile.vue` component
- **API Endpoints:**
  - `GET /api/user/profile` - fetch user profile
  - `PUT /api/user/profile` - update profile information
  - `PUT /api/user/password` - change password
  - `POST /api/user/avatar` - upload profile picture

### Features
- Display user details (name, email, role, created date)
- Edit profile information (name, email)
- Change password with validation
- Upload profile picture/avatar
- Account settings and preferences
- Activity log/session history

### UI Components
- Profile information card
- Editable form fields with validation
- Password change form with strength indicator
- File upload component for avatar
- Settings toggles and preferences
- Success/error notifications

### Technical Implementation
```vue
<template>
  <div class="profile-page">
    <div class="profile-header">
      <div class="avatar-section">
        <img :src="user.avatar || defaultAvatar" alt="Profile" />
        <button @click="openAvatarUpload" class="btn-secondary">
          Change Photo
        </button>
      </div>
      <div class="user-info">
        <h1>{{ user.name }}</h1>
        <p class="role-badge">{{ user.role }}</p>
      </div>
    </div>
    
    <div class="profile-content">
      <ProfileForm v-model="profileData" @save="updateProfile" />
      <PasswordForm @change="changePassword" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ProfileForm, PasswordForm } from '@/components'
import { useAuth } from '@/stores/auth'
import { useProfile } from '@/composables/useProfile'

const auth = useAuth()
const { user, updateProfile, changePassword } = useProfile()
const profileData = ref({})

onMounted(async () => {
  await fetchUserProfile()
})
</script>
```

---

## General Notes

### Development Guidelines
- All pages must enforce permission checks both in routing and UI components
- Use the `CanAccess` component to conditionally render UI elements based on permissions
- Follow the existing UI/UX style using Tailwind CSS and Vue 3 composition API
- Ensure proper error handling and loading states
- Pages should be responsive and accessible
- Implement proper SEO meta tags for each page

### Routing Configuration
Add the following routes to `resources/js/router/index.js`:

```javascript
const routes = [
  // Existing routes...
  {
    path: '/material-types',
    name: 'MaterialTypes',
    component: () => import('@/pages/MaterialTypes.vue'),
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.material-types',
      title: 'Material Types'
    }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: () => import('@/pages/Reports.vue'),
    meta: {
      requiresAuth: true,
      requiresPermission: 'view.reports',
      title: 'Reports'
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/pages/Profile.vue'),
    meta: {
      requiresAuth: true,
      title: 'Profile'
    }
  }
]
```

### Required Composables
Create the following composables for data management:

- `useMaterialTypes.js` - Material types CRUD operations
- `useReports.js` - Reports data fetching and filtering
- `useProfile.js` - User profile management
- `useDashboard.js` - Dashboard statistics and data

### Component Structure
```
resources/js/
├── pages/
│   ├── Dashboard.vue (existing)
│   ├── MaterialTypes.vue (new)
│   ├── Reports.vue (new)
│   └── Profile.vue (new)
├── components/
│   ├── CanAccess.vue (existing)
│   ├── ProfileForm.vue (new)
│   ├── PasswordForm.vue (new)
│   ├── ReportCard.vue (new)
│   ├── DateRangePicker.vue (new)
│   └── ExportButton.vue (new)
└── composables/
    ├── useMaterialTypes.js (new)
    ├── useReports.js (new)
    ├── useProfile.js (new)
    └── useDashboard.js (new)
```

### Testing Requirements
- Unit tests for all new components
- Integration tests for API interactions
- E2E tests for critical user flows
- Permission-based access testing

### Performance Considerations
- Implement lazy loading for large datasets
- Use pagination for material types and reports
- Optimize images and assets
- Implement caching strategies for frequently accessed data

### Accessibility Requirements
- ARIA labels for interactive elements
- Keyboard navigation support
- Screen reader compatibility
- Color contrast compliance (WCAG 2.1 AA)

---

This document should guide the frontend development team in implementing the required pages with proper routing, permission enforcement, and UI features consistent with the existing application architecture.