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
  ) {
    console.log('InterventionCreateComponent initialisé');
  }

  onSubmit(): void {
    console.log('Début de la soumission du formulaire');
    console.log('Données de l\'intervention à créer:', this.intervention);

    this.loading = true;
    this.error = '';
    this.success = '';

    if (!this.intervention.TypeIntervention || !this.intervention.Description || 
        !this.intervention.DebutIntervention || !this.intervention.FinIntervention || 
        !this.intervention.ClientID) {
      console.error('Validation échouée - Champs manquants:', {
        typeIntervention: !this.intervention.TypeIntervention,
        description: !this.intervention.Description,
        debutIntervention: !this.intervention.DebutIntervention,
        finIntervention: !this.intervention.FinIntervention,
        clientId: !this.intervention.ClientID
      });
      this.error = 'Veuillez remplir tous les champs obligatoires';
      this.loading = false;
      return;
    }

    console.log('Validation réussie, envoi à InterventionService');

    this.interventionService.createIntervention(this.intervention).subscribe({
      next: (response: ApiResponse<void>) => {
        console.log('Réponse du serveur:', response);
        if (response.status === 'success') {
          console.log('Création réussie, redirection dans 1.5s');
          this.success = 'Intervention créée avec succès';
          setTimeout(() => {
            this.router.navigate(['/prepose/interventions']);
          }, 1500);
        } else {
          console.error('Erreur serveur:', response.message);
          this.error = response.message || 'Erreur lors de la création de l\'intervention';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        console.error('Erreur technique:', error);
        this.error = 'Erreur lors de la création de l\'intervention: ' + error.message;
        this.loading = false;
      }
    });
  }

  onTechnicienSelected(technicienId: number): void {
    console.log('Technicien sélectionné:', technicienId);
    this.intervention.TechnicienID = technicienId;
  }
}