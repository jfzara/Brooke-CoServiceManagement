import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Intervention, STATUS_TYPES } from '../../../../models/intervention.model';

@Component({
  selector: 'app-intervention-form',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  template: `
    <form [formGroup]="form" (ngSubmit)="onSubmit()" class="intervention-form">
      <div class="form-group">
        <label for="typeIntervention">Type d'intervention*</label>
        <select 
          id="typeIntervention"
          formControlName="TypeIntervention"
          class="form-control"
        >
          <option value="">Sélectionnez un type</option>
          <option value="Installation Fibre">Installation Fibre</option>
          <option value="Dépannage Internet">Dépannage Internet</option>
          <option value="Configuration WiFi">Configuration WiFi</option>
          <option value="Installation TV">Installation TV</option>
          <option value="Maintenance Box">Maintenance Box</option>
        </select>
        <div class="error-message" *ngIf="form.get('TypeIntervention')?.errors?.['required'] && form.get('TypeIntervention')?.touched">
          Le type d'intervention est requis
        </div>
      </div>

      <div class="form-group">
        <label for="description">Description*</label>
        <textarea 
          id="description"
          formControlName="Description"
          class="form-control"
          rows="4"
        ></textarea>
        <div class="error-message" *ngIf="form.get('Description')?.errors?.['required'] && form.get('Description')?.touched">
          La description est requise
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="debutIntervention">Date et heure de début*</label>
          <input 
            type="datetime-local"
            id="debutIntervention"
            formControlName="DebutIntervention"
            class="form-control"
          >
          <div class="error-message" *ngIf="form.get('DebutIntervention')?.errors?.['required'] && form.get('DebutIntervention')?.touched">
            La date de début est requise
          </div>
        </div>

        <div class="form-group">
          <label for="finIntervention">Date et heure de fin*</label>
          <input 
            type="datetime-local"
            id="finIntervention"
            formControlName="FinIntervention"
            class="form-control"
          >
          <div class="error-message" *ngIf="form.get('FinIntervention')?.errors?.['required'] && form.get('FinIntervention')?.touched">
            La date de fin est requise
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="clientId">Client ID*</label>
        <input 
          type="number"
          id="clientId"
          formControlName="ClientID"
          class="form-control"
        >
        <div class="error-message" *ngIf="form.get('ClientID')?.errors?.['required'] && form.get('ClientID')?.touched">
          L'ID du client est requis
        </div>
      </div>

      <div class="form-group" *ngIf="showStatus">
        <label for="statut">Statut</label>
        <select 
          id="statut"
          formControlName="StatutIntervention"
          class="form-control"
        >
          <option *ngFor="let status of statusList" [value]="status">{{status}}</option>
        </select>
      </div>

      <div class="form-group">
        <label for="commentaires">Commentaires</label>
        <textarea 
          id="commentaires"
          formControlName="Commentaires"
          class="form-control"
          rows="3"
        ></textarea>
      </div>

      <div class="form-actions">
        <button type="button" class="btn btn-secondary" (click)="onCancel()">
          Annuler
        </button>
        <button type="submit" class="btn btn-primary" [disabled]="form.invalid || loading">
          {{submitButtonText}}
        </button>
      </div>
    </form>
  `,
  styles: [`
    .intervention-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .form-control {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 100px;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 20px;
    }

    .btn {
      padding: 8px 16px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-weight: 500;
    }

    .btn-primary {
      background: #4CAF50;
      color: white;
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
    }

    .btn:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    .error-message {
      color: #dc3545;
      font-size: 0.875rem;
    }
  `]
})
export class InterventionFormComponent {
  @Input() intervention?: Partial<Intervention>;
  @Input() loading = false;
  @Input() showStatus = false;
  @Input() submitButtonText = 'Enregistrer';
  
  @Output() formSubmit = new EventEmitter<Partial<Intervention>>();
  @Output() formCancel = new EventEmitter<void>();

  form: FormGroup;
  statusList = Object.values(STATUS_TYPES);

  constructor(private fb: FormBuilder) {
    this.form = this.fb.group({
      TypeIntervention: ['', Validators.required],
      Description: ['', Validators.required],
      DebutIntervention: ['', Validators.required],
      FinIntervention: ['', Validators.required],
      ClientID: ['', Validators.required],
      StatutIntervention: [STATUS_TYPES.PENDING],
      Commentaires: ['']
    });
  }

  ngOnInit() {
    if (this.intervention) {
      this.form.patchValue(this.intervention);
    }
  }

  onSubmit() {
    if (this.form.valid) {
      this.formSubmit.emit(this.form.value);
    }
  }

  onCancel() {
    this.formCancel.emit();
  }
}