// src/app/technicien/interventions/intervention-edit/intervention-edit.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { InterventionService } from '@app/core/services/intervention.service';
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
          <label for="status">Statut</label>
          <select id="status" formControlName="StatutIntervention">
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
    }
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .form-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
    }
    .error-message {
      color: #dc3545;
      margin-top: 10px;
    }
  `]
})
export class InterventionEditComponent implements OnInit {
  editForm: FormGroup;
  intervention: Intervention | null = null;
  loading = false;
  error = '';
  readonly STATUS_TYPES = STATUS_TYPES;

  constructor(
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private interventionService: InterventionService
  ) {
    this.editForm = this.formBuilder.group({
      StatutIntervention: ['', Validators.required],
      Commentaires: ['']
    });
  }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadIntervention(parseInt(id, 10));
    }
  }

  loadIntervention(id: number): void {
    this.loading = true;
    this.interventionService.getInterventionById(id).subscribe({
      next: (response: ApiResponse<Intervention>) => {
        if (response.status === 'success' && response.data) {
          this.intervention = response.data;
          this.editForm.patchValue({
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
          this.router.navigate(['/technicien/interventions']);
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
    this.router.navigate(['/technicien/interventions']);
  }
}