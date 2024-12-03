// src/app/prepose/interventions/intervention-edit/intervention-edit.component.ts

import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { InterventionService } from '@app/core/services/intervention.service';
import { TechnicienService } from '@app/core/services/technicien.service';
import { Intervention, STATUS_TYPES } from '@app/models/intervention.model';
import { ApiResponse } from '@app/models/api.model';

@Component({
  selector: 'app-intervention-edit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <div class="edit-container">
      <h2>Modifier l'intervention</h2>
      <form [formGroup]="editForm" (ngSubmit)="onSubmit()" *ngIf="intervention">
        <div class="form-group">
          <label for="technicien">Technicien</label>
          <select id="technicien" formControlName="TechnicienID" required>
            <option value="">Sélectionner un technicien</option>
            <option *ngFor="let tech of techniciens" [value]="tech.TechnicienID">
              {{tech.Prenom}} {{tech.Nom}}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="type">Type d'intervention</label>
          <select id="type" formControlName="TypeIntervention" required>
            <option value="">Sélectionner un type</option>
            <option value="installation">Installation</option>
            <option value="maintenance">Maintenance</option>
            <option value="depannage">Dépannage</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea 
            id="description" 
            formControlName="Description"
            rows="3"
          ></textarea>
        </div>

        <div class="form-group">
          <label for="debut">Date et heure de début</label>
          <input 
            type="datetime-local" 
            id="debut" 
            formControlName="DebutIntervention"
            required
          >
        </div>

        <div class="form-group">
          <label for="fin">Date et heure de fin</label>
          <input 
            type="datetime-local" 
            id="fin" 
            formControlName="FinIntervention"
            required
          >
        </div>

        <div class="form-group">
          <label for="status">Statut</label>
          <select id="status" formControlName="StatutIntervention" required>
            <option *ngFor="let status of getStatusOptions()" [value]="status">
              {{status}}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="commentaires">Commentaires</label>
          <textarea 
            id="commentaires" 
            formControlName="Commentaires"
            rows="4"
          ></textarea>
        </div>

        <div class="form-actions">
          <button type="button" (click)="goBack()">Annuler</button>
          <button type="submit" [disabled]="editForm.invalid || loading">
            {{loading ? 'Enregistrement...' : 'Enregistrer'}}
          </button>
        </div>
      </form>

      <div class="error-message" *ngIf="error">{{error}}</div>
    </div>
  `,
  styles: [`
    .edit-container {
      padding: 20px;
      max-width: 600px;
      margin: 0 auto;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }
    .form-group select,
    .form-group textarea,
    .form-group input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    .form-group textarea {
      resize: vertical;
    }
    .form-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
    }
    .form-actions button {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 500;
    }
    .form-actions button[type="submit"] {
      background-color: #007bff;
      color: white;
    }
    .form-actions button[type="submit"]:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
    .form-actions button[type="button"] {
      background-color: #6c757d;
      color: white;
    }
    .error-message {
      color: #dc3545;
      margin-top: 10px;
      padding: 10px;
      background-color: #f8d7da;
      border-radius: 4px;
    }
  `]
})
export class InterventionEditComponent implements OnInit {
  editForm: FormGroup;
  intervention: Intervention | null = null;
  loading = false;
  error = '';
  readonly STATUS_TYPES = STATUS_TYPES;
  techniciens: any[] = [];

  constructor(
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private interventionService: InterventionService,
    private technicienService: TechnicienService
  ) {
    this.editForm = this.formBuilder.group({
      TechnicienID: ['', Validators.required],
      TypeIntervention: ['', Validators.required],
      Description: [''],
      DebutIntervention: ['', Validators.required],
      FinIntervention: ['', Validators.required],
      StatutIntervention: ['', Validators.required],
      Commentaires: ['']
    });
  }

  ngOnInit(): void {
    this.loadTechniciens();
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadIntervention(parseInt(id, 10));
    }
  }

  loadTechniciens(): void {
    this.technicienService.getAllTechniciens().subscribe({
      next: (response: any) => {
        if (response.status === 'success') {
          this.techniciens = response.data;
        }
      },
      error: (error) => {
        console.error('Erreur lors du chargement des techniciens:', error);
        this.error = 'Erreur lors du chargement des techniciens';
      }
    });
  }

  loadIntervention(id: number): void {
    this.loading = true;
    this.interventionService.getInterventionById(id).subscribe({
      next: (response: ApiResponse<Intervention>) => {
        if (response.status === 'success' && response.data) {
          this.intervention = response.data;
          // Format dates for datetime-local input
          const debut = new Date(this.intervention.DebutIntervention)
            .toISOString().slice(0, 16);
          const fin = new Date(this.intervention.FinIntervention)
            .toISOString().slice(0, 16);
          
          this.editForm.patchValue({
            TechnicienID: this.intervention.TechnicienID,
            TypeIntervention: this.intervention.TypeIntervention,
            Description: this.intervention.Description,
            DebutIntervention: debut,
            FinIntervention: fin,
            StatutIntervention: this.intervention.StatutIntervention,
            Commentaires: this.intervention.Commentaires || ''
          });
        } else {
          this.error = 'Erreur lors du chargement de l\'intervention';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        this.error = 'Erreur lors du chargement de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }

  onSubmit(): void {
    if (this.editForm.invalid || !this.intervention?.InterventionID) {
      return;
    }

    this.loading = true;
    const updatedData: Partial<Intervention> = {
      ...this.intervention,
      ...this.editForm.value
    };

    this.interventionService.updateIntervention(
      this.intervention.InterventionID,
      updatedData
    ).subscribe({
      next: (response: ApiResponse<void>) => {
        if (response.status === 'success') {
          this.router.navigate(['/prepose/interventions']);
        } else {
          this.error = 'Erreur lors de la mise à jour de l\'intervention';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        this.error = 'Erreur lors de la mise à jour de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }

  getStatusOptions(): string[] {
    return Object.values(STATUS_TYPES);
  }

  goBack(): void {
    this.router.navigate(['/prepose/interventions']);
  }
}