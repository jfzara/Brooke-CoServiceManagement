import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { InterventionService } from '../../services/intervention.service';
import { Intervention, STATUS_TYPES } from '../../models/intervention.model';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-planning-technicien',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './planning-technicien.component.html',
  styleUrls: ['./planning-technicien.component.css']
})
export class PlanningTechnicienComponent implements OnInit {
  interventions: Intervention[] = [];
  selectedIntervention: Intervention | null = null;
  showModal = false;
  loading = false;
  error: string | null = null;
  readonly STATUS_TYPES = STATUS_TYPES;

  constructor(
    private interventionService: InterventionService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.loadInterventions();
  }

  loadInterventions() {
    this.loading = true;
    this.error = null;

    const currentUser = this.authService.currentUserValue;
    if (!currentUser?.technicienId) {
      this.error = 'Utilisateur non connecté ou non technicien';
      this.loading = false;
      return;
    }

    this.interventionService.getTechnicienInterventions(currentUser.technicienId)
      .subscribe({
        next: (response) => {
          console.log('Réponse API:', response);
          if (response.status === 'success' && response.data) {
            this.interventions = response.data;
            console.log('Interventions chargées:', this.interventions);
          } else {
            this.error = response.message || 'Erreur lors du chargement des interventions';
          }
          this.loading = false;
        },
        error: (error) => {
          console.error('Erreur:', error);
          this.error = 'Erreur lors du chargement des interventions';
          this.loading = false;
        }
      });
  }

  getStatusClass(status: string): string {
    switch (status.toLowerCase()) {
      case STATUS_TYPES.PENDING.toLowerCase():
        return 'status-pending';
      case STATUS_TYPES.IN_PROGRESS.toLowerCase():
        return 'status-progress';
      case STATUS_TYPES.COMPLETED.toLowerCase():
        return 'status-completed';
      case STATUS_TYPES.CANCELLED.toLowerCase():
        return 'status-cancelled';
      default:
        return '';
    }
  }

  editIntervention(intervention: Intervention) {
    this.selectedIntervention = intervention;
    this.showModal = true;
  }

  closeModal() {
    this.showModal = false;
    this.selectedIntervention = null;
  }

  startIntervention(intervention: Intervention) {
    this.updateStatus(intervention, STATUS_TYPES.IN_PROGRESS);
  }

  completeIntervention(intervention: Intervention) {
    this.updateStatus(intervention, STATUS_TYPES.COMPLETED);
  }

  cancelIntervention(intervention: Intervention) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      if (intervention.InterventionID) {
        this.interventionService.deleteIntervention(intervention.InterventionID)
          .subscribe({
            next: (response) => {
              if (response.status === 'success') {
                this.loadInterventions();
              } else {
                this.error = response.message || 'Erreur lors de l\'annulation';
              }
            },
            error: (error) => {
              console.error('Erreur lors de l\'annulation:', error);
              this.error = 'Erreur lors de l\'annulation de l\'intervention';
            }
          });
      }
    }
  }

  private updateStatus(intervention: Intervention, newStatus: string) {
    if (intervention.InterventionID) {
      this.interventionService.updateStatusIntervention(intervention.InterventionID, newStatus)
        .subscribe({
          next: (response) => {
            if (response.status === 'success') {
              this.loadInterventions();
            } else {
              this.error = response.message || 'Erreur lors de la mise à jour du statut';
            }
          },
          error: (error) => {
            console.error('Erreur lors de la mise à jour du statut:', error);
            this.error = 'Erreur lors de la mise à jour du statut';
          }
        });
    }
  }
}