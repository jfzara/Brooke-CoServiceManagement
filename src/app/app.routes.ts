import { Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';

export const routes: Routes = [
  { 
    path: '', 
    redirectTo: '/login', 
    pathMatch: 'full' 
  },
  {
    path: 'login',
    loadComponent: () => import('./login/login.component')
      .then(m => m.LoginComponent)
  },
  {
    path: 'prepose',
    loadComponent: () => import('./prepose/prepose.component')
      .then(m => m.PreposeComponent),
    canActivate: [AuthGuard],
    data: { roles: ['prepose'] }
  },
  {
    path: 'technicien',
    loadComponent: () => import('./technicien/technicien.component')
      .then(m => m.TechnicienComponent),
    canActivate: [AuthGuard],
    data: { roles: ['technicien'] }
  },
  {
    path: 'interventions',
    loadComponent: () => import('./technicien/technicien.component')
      .then(m => m.TechnicienComponent),
    canActivate: [AuthGuard],
    data: { roles: ['technicien'] }
  },
  {
    path: 'planning',
    loadComponent: () => import('./pages/planning-technicien/planning-technicien.component')
      .then(m => m.PlanningTechnicienComponent),
    canActivate: [AuthGuard],
    data: { roles: ['technicien'] }
  },
  {
    path: 'intervention/edit/:id',
    loadComponent: () => import('./pages/intervention-edit/intervention-edit.component')
      .then(m => m.InterventionEditComponent),
    canActivate: [AuthGuard],
    data: { roles: ['technicien'] }
  },
  { path: '**', redirectTo: '/login' }
];