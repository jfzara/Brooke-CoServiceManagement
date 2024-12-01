import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { InterventionService } from '../../../services/intervention.service';
import { Intervention, STATUS_TYPES } from '../../../models/intervention.model';

@Component({
  selector: 'app-intervention-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="detail-container" *ngIf="intervention">
      <div class="header">
        <h2>Détail de l'intervention #{{intervention.InterventionID}}</h2>
        <div class="actions">
          <button class="btn btn-edit" (click)="editIntervention()">Modifier</button>
          <button class="btn btn-delete" (click)="deleteIntervention()">Supprimer</button>
        </div>
      </div>

      <div class="content">
        <div class="info-group">
          <label>Type d'intervention</label>
          <p>{{intervention.TypeIntervention}}</p>
        </div>

        <div class="info-group">
          <label>Description</label>
          <p>{{intervention.Description}}</p>
        </div>

        <div class="info-group">
          <label>Client</label>
          <p>Client #{{intervention.ClientID}}</p>
        </div>

        <div class="info-group">
          <label>Technicien</label>
          <p>{{intervention.TechnicienID ? 'Technicien #' + intervention.TechnicienID : 'Non assigné'}}</p>
        </div>

        <div class="info-group">
          <label>Statut</label>
          <p class="status" [ngClass]="'status-' + intervention.StatutIntervention.toLowerCase()">
            {{intervention.StatutIntervention}}
          </p>
        </div>

        <div class="info-group">
          <label>Date et heure de début</label>
          <p>{{intervention.DebutIntervention | date:'dd/MM/yyyy HH:mm'}}</p>
        </div>

        <div class="info-group">
          <label>Date et heure de fin</label>
          <p>{{intervention.FinIntervention | date:'dd/MM/yyyy HH:mm'}}</p>
        </div>

        <div class="info-group" *ngIf="intervention.Commentaires">
          <label>Commentaires</label>
          <p>{{intervention.Commentaires}}</p>
        </div>
      </div>
    </div>

    <div class="loading" *ngIf="loading">Chargement...</div>
    <div class="error" *ngIf="error">{{error}}</div>
  `,
  styles: [`
    .detail-container {
      background: white;
      border-radius: 8px;
      padding: 24px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    .actions {
      display: flex;
      gap: 12px;
    }

    .btn {
      padding: 8px 16px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-weight: 500;
    }

    .btn-edit {
      background: #2196F3;
      color: white;
    }

    .btn-delete {
      background: #f44336;
      color: white;
    }

    .content {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
    }

    .info-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .info-group label {
      font-weight: 500;
      color: #666;
    }

    .status {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 4px;
      font-weight: 500;
    }

    .status-planifiée { background: #FFF3CD; color: #856404; }
    .status-en.cours { background: #D4EDDA; color: #155724; }
    .status-terminée { background: #CCE5FF; color: #004085; }
    .status-annulée { background: #F8D7DA; color: #721C24; }

    .loading {
      text-align: center;
      padding: 24px;
      color: #666;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      padding: 12px;
      border-radius: 4px;
      margin-top: 12px;
    }
  `]
})
export class InterventionDetailComponent implements OnInit {
  intervention: Intervention | null = null;
  loading = false;
  error = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private interventionService: InterventionService
  ) {}

  ngOnInit() {
    this.loadIntervention();
  }

  private loadIntervention() {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) {
      this.error = 'ID de l\'intervention non fourni';
      return;
    }

    this.loading = true;
    this.interventionService.getInterventionById(parseInt(id, 10)).subscribe({
      next: (response) => {
        if (response.status === 'success' && response.data) {
          this.intervention = response.data;
        } else {
          this.error = 'Erreur lors du chargement de l\'intervention';
        }
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Erreur lors du chargement de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }

  editIntervention() {
    if (this.intervention?.InterventionID) {
      this.router.navigate(['/prepose/interventions/edit', this.intervention.InterventionID]);
    }
  }

  deleteIntervention() {
    if (!this.intervention?.InterventionID || !confirm('Êtes-vous sûr de vouloir supprimer cette intervention ?')) {
      return;
    }

    this.loading = true;
    this.interventionService.deleteIntervention(this.intervention.InterventionID).subscribe({
      next: (response) => {
        if (response.status === 'success') {
          this.router.navigate(['/prepose/interventions']);
        } else {
          this.error = 'Erreur lors de la suppression de l\'intervention';
        }
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Erreur lors de la suppression de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }
}