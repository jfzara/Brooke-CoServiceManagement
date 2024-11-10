import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { PreposeComponent } from './prepose/prepose.component';
import { TechnicienComponent } from './technicien/technicien.component';

export const routes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'prepose', component: PreposeComponent },
  { path: 'technicien', component: TechnicienComponent } 
];