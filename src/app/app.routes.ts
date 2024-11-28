import { Routes } from '@angular/router';

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
      .then(m => m.PreposeComponent)
  },
  {
    path: 'technicien',
    loadComponent: () => import('./technicien/technicien.component')
      .then(m => m.TechnicienComponent)
  },
  {
    path: 'planning',
    loadComponent: () => import('./pages/planning-technicien/planning-technicien.component')
      .then(m => m.PlanningTechnicienComponent)
  },
  {
    path: 'intervention/edit/:id',
    loadComponent: () => import('./pages/intervention-edit/intervention-edit.component')
      .then(m => m.InterventionEditComponent)
  }
];