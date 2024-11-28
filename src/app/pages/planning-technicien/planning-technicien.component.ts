import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { InterventionService, Intervention, STATUS_TYPES } from '../../services/intervention.service';

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

  constructor(private interventionService: InterventionService) {}

  ngOnInit() {
    this.loadInterventions();
  }

  loadInterventions() {
    this.loading = true;
    this.error = null;
    const technicienId = 2; // À remplacer par l'ID du technicien connecté
    this.interventionService.getPlanningByTechnicienWithMoreInfos(technicienId)
      .subscribe({
        next: (data) => {
          this.interventions = data;
          this.loading = false;
        },
        error: (error) => {
          this.error = 'Erreur lors du chargement des interventions';
          this.loading = false;
          console.error('Erreur:', error);
        }
      });
  }

  getStatusClass(status: string): string {
    switch (status) {
      case STATUS_TYPES.PENDING:
        return 'status-pending';
      case STATUS_TYPES.IN_PROGRESS:
        return 'status-progress';
      case STATUS_TYPES.COMPLETED:
        return 'status-completed';
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
      this.interventionService.deleteIntervention(intervention.InterventionID!)
        .subscribe({
          next: () => {
            this.loadInterventions();
          },
          error: (error) => {
            console.error('Erreur lors de l\'annulation:', error);
          }
        });
    }
  }

  private updateStatus(intervention: Intervention, newStatus: string) {
    this.interventionService.updateStatusIntervention({
      InterventionID: intervention.InterventionID!,
      StatutIntervention: newStatus
    }).subscribe({
      next: () => {
        this.loadInterventions();
      },
      error: (error) => {
        console.error('Erreur lors de la mise à jour du statut:', error);
      }
    });
  }
}