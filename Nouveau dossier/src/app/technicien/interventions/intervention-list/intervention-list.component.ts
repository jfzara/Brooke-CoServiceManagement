// src/app/technicien/interventions/intervention-list/intervention-list.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { InterventionService } from '../../../core/services/intervention.service';
import { AuthService } from '../../../core/services/auth.service';
import { Intervention, STATUS_TYPES } from '../../../models/intervention.model';
import { ApiResponse } from '../../../models/api.model';

@Component({
  selector: 'app-intervention-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="interventions-container">
      <h2>Mes Interventions</h2>
      
      <div *ngIf="loading" class="loading">
        Chargement des interventions...
      </div>

      <div *ngIf="error" class="error">
        {{error}}
      </div>

      <div *ngIf="!loading && !error" class="interventions-list">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Client</th>
              <th>Type</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let intervention of interventions">
              <td>{{intervention.DebutIntervention | date:'dd/MM/yyyy HH:mm'}}</td>
              <td>Client {{intervention.ClientID}}</td>
              <td>{{intervention.TypeIntervention}}</td>
              <td>{{intervention.StatutIntervention}}</td>
              <td>
                <a [routerLink]="['../intervention/edit', intervention.InterventionID]">
                  Modifier
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `,
  styles: [`
    .interventions-container {
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .loading, .error {
      padding: 20px;
      text-align: center;
    }
    .error {
      color: red;
    }
  `]
})
export class InterventionListComponent implements OnInit {
  interventions: Intervention[] = [];
  loading = false;
  error = '';

  constructor(
    private interventionService: InterventionService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadInterventions();
  }

  private loadInterventions(): void {
    const technicienId = this.authService.userProfile.technicienId;
    if (!technicienId) {
      this.error = 'ID du technicien non trouvé';
      return;
    }

    this.loading = true;
    this.interventionService.getTechnicienInterventions(technicienId)
      .subscribe({
        next: (response: ApiResponse<Intervention[]>) => {
          if (response.status === 'success' && response.data) {
            this.interventions = response.data;
          } else {
            this.error = response.message || 'Erreur lors du chargement des interventions';
          }
          this.loading = false;
        },
        error: (error: Error) => {
          this.error = 'Erreur lors du chargement des interventions';
          this.loading = false;
          console.error('Erreur:', error);
        }
      });
  }
}