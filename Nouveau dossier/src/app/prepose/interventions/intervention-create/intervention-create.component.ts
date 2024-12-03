import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { InterventionService } from '../../../core/services/intervention.service';
import { Intervention } from '../../../models/intervention.model';
import { TechnicienSelectorComponent } from '../technicien-selector/technicien-selector.component';

@Component({
  selector: 'app-intervention-create',
  templateUrl: './intervention-create.component.html',
  styleUrls: ['./intervention-create.component.css'],
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TechnicienSelectorComponent]
})
export class InterventionCreateComponent implements OnInit {
  interventionForm: FormGroup;
  loading = false;
  error: string | null = null;
  successMessage: string | null = null;
  selectedTechnicienId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private interventionService: InterventionService,
    private router: Router
  ) {
    this.interventionForm = this.fb.group({
      TypeIntervention: ['', [Validators.required]],
      Description: ['', [Validators.required]],
      DebutIntervention: ['', [Validators.required]],
      FinIntervention: ['', [Validators.required]],
      ClientID: ['', [Validators.required]],
      TechnicienID: [null, [Validators.required]],
      StatutIntervention: ['planifiee'],
      Commentaires: ['']
    });
  }

  ngOnInit(): void {}

  onDateChange(): void {
    const debut = this.interventionForm.get('DebutIntervention')?.value;
    const fin = this.interventionForm.get('FinIntervention')?.value;
    
    if (debut && fin && new Date(fin) < new Date(debut)) {
      this.interventionForm.get('FinIntervention')?.setErrors({ 'dateInvalide': true });
    }
  }

  onTechnicienSelected(technicienId: number): void {
    console.log('Technicien sélectionné:', technicienId);
    this.selectedTechnicienId = technicienId;
    this.interventionForm.patchValue({
      TechnicienID: technicienId
    });
  }

  onSubmit(): void {
    if (this.interventionForm.valid) {
      this.loading = true;
      this.error = null;
      this.successMessage = null;

      const intervention: Intervention = {
        ...this.interventionForm.value,
        DateCreation: new Date().toISOString()
      };
      
      console.log('Envoi de l\'intervention:', intervention);

      this.interventionService.createIntervention(intervention).subscribe({
        next: (response) => {
          console.log('Réponse création:', response);
          this.loading = false;
          this.successMessage = 'Intervention créée avec succès';
          setTimeout(() => {
            this.router.navigate(['/prepose/interventions']);
          }, 2000);
        },
        error: (err) => {
          console.error('Erreur création:', err);
          this.loading = false;
          this.error = 'Erreur lors de la création de l\'intervention: ' + (err.message || err);
        }
      });
    } else {
      this.error = 'Veuillez remplir tous les champs obligatoires';
      console.log('Formulaire invalide:', this.interventionForm.errors);
    }
  }
}