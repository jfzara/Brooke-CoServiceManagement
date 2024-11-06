// src/app/app.routes.ts
import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { PreposeComponent } from './prepose/prepose.component';

export const routes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'prepose', component: PreposeComponent },
];