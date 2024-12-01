import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { InterventionService } from '../../../core/services/intervention.service';
import { ApiResponse } from '../../../models/api.model';
import { Intervention, STATUS_TYPES } from '../../../models/intervention.model';
import { TechnicienSelectorComponent } from '../technicien-selector/technicien-selector.component';

@Component({
  selector: 'app-intervention-create',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    RouterModule,
    TechnicienSelectorComponent
  ],
  templateUrl: './intervention-create.component.html',
  styleUrls: ['./intervention-create.component.css']
})
export class InterventionCreateComponent {
  intervention: Partial<Intervention> = {
    TypeIntervention: '',
    Description: '',
    DebutIntervention: '',
    FinIntervention: '',
    StatutIntervention: STATUS_TYPES.PENDING,
    ClientID: undefined,
    TechnicienID: undefined
  };

  loading = false;
  error = '';
  success = '';

  constructor(
    private interventionService: InterventionService,
    private router: Router
  ) {}

  onSubmit(): void {
    this.loading = true;
    this.error = '';
    this.success = '';

    if (!this.intervention.TypeIntervention || !this.intervention.Description || 
        !this.intervention.DebutIntervention || !this.intervention.FinIntervention || 
        !this.intervention.ClientID) {
      this.error = 'Veuillez remplir tous les champs obligatoires';
      this.loading = false;
      return;
    }

    this.interventionService.createIntervention(this.intervention).subscribe({
      next: (response: ApiResponse<void>) => {
        if (response.status === 'success') {
          this.success = 'Intervention créée avec succès';
          setTimeout(() => {
            this.router.navigate(['/prepose/interventions']);
          }, 1500);
        } else {
          this.error = response.message || 'Erreur lors de la création de l\'intervention';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        this.error = 'Erreur lors de la création de l\'intervention: ' + error.message;
        this.loading = false;
      }
    });
  }

  onTechnicienSelected(technicienId: number): void {
    this.intervention.TechnicienID = technicienId;
  }
}