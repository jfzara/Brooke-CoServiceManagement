import { Routes } from '@angular/router';
import { AuthGuard } from '@app/core/guards/auth.guard';

export const routes: Routes = [
  { 
    path: '', 
    redirectTo: '/login', 
    pathMatch: 'full' 
  },
  {
    path: 'login',
    loadComponent: () => import('@app/login/login.component')
      .then(m => m.LoginComponent)
  },
  {
    path: 'prepose',
    loadComponent: () => import('@app/prepose/dashboard/prepose-dashboard.component')
      .then(m => m.PreposeDashboardComponent),
    canActivate: [AuthGuard],
    data: { roles: ['prepose'] },
    children: [
      {
        path: '',
        redirectTo: 'interventions',
        pathMatch: 'full'
      },
      {
        path: 'interventions',
        loadComponent: () => import('@app/prepose/interventions/intervention-list/intervention-list.component')
          .then(m => m.InterventionListComponent)
      },
      {
        path: 'create',
        loadComponent: () => import('@app/prepose/interventions/intervention-create/intervention-create.component')
          .then(m => m.InterventionCreateComponent)
      },
      {
        path: 'edit/:id',
        loadComponent: () => import('@app/prepose/interventions/intervention-edit/intervention-edit.component')
          .then(m => m.InterventionEditComponent)
      }
    ]
  },
  {
    path: 'technicien',
    loadComponent: () => import('@app/technicien/technicien.component')
      .then(m => m.TechnicienComponent),
    canActivate: [AuthGuard],
    data: { roles: ['technicien'] },
    children: [
      {
        path: '',
        redirectTo: 'interventions',
        pathMatch: 'full'
      },
      {
        path: 'interventions',
        loadComponent: () => import('@app/technicien/interventions/intervention-list/intervention-list.component')
          .then(m => m.InterventionListComponent)
      },
      {
        path: 'edit/:id',
        loadComponent: () => import('@app/technicien/interventions/intervention-edit/intervention-edit.component')
          .then(m => m.InterventionEditComponent)
      },
      {
        path: 'planning',
        loadComponent: () => import('@app/technicien/planning/planning-technicien.component')
          .then(m => m.PlanningTechnicienComponent)
      }
    ]
  },
  { path: '**', redirectTo: '/login' }
];