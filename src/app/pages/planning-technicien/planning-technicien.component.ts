import { Component, OnInit } from '@angular/core';
import { InterventionService } from '../../services/intervention.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-planning-technicien',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './planning-technicien.component.html',
  styleUrl: './planning-technicien.component.css',
})
export class PlanningTechnicienComponent implements OnInit {
  showModal = false;
  selectedIntervention: any;
  description: string ="";
  isPushOut = false;
  interventions: any[] = [];
  technicienID: number = 2;

  constructor(
    private interventionService: InterventionService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadPlannings(this.technicienID);
  }

  editIntervention(id: number) {
    this.router.navigate(['/intervention/edit', id]);
  }

  

  loadPlannings(technicienID: number) {
    this.interventionService
      .getPlanningByTechnicienWithMoreInfos(technicienID)
      .subscribe((planningData: any[]) => {
        console.log(planningData);
        this.interventions = planningData;
      });
  }

  openModal(intervention: any) {
    this.selectedIntervention = intervention;
    this.showModal = true;
  }

  openModalToFinalize(intervention: any, ) {
    this.selectedIntervention = intervention;
    this.showModal = true;
    this.isPushOut = true;
  }

  closeModal() {
    this.showModal = false;
    this.isPushOut = false;
  }

  getStatusClass(statut: string): string {
    switch (statut) {
      case 'Assignee':
        return 'status-assignee';
      case 'Activee':
        return 'status-activee';
      case 'En cours':
        return 'status-en-cours';
      case 'Terminee':
        return 'status-terminee';
      case 'Annulee':
        return 'status-annulee';
      default:
        return '';
    }
  }

  updateStatusWithHour(intervention: any, statut: string){
    const data = {
      InterventionID: intervention.InterventionID,
      Statut: statut,
      Date: intervention.DateIntervention,
      Heure: this.getCurrentTime()
    }
    this.interventionService.updateStatusIntervention(data).subscribe(result => {
      console.log(result);
      this.loadPlannings(this.technicienID);
    })
  }

  updateStatusWithoutHour(intervention: any, statut: string){
    const data = {
      InterventionID: intervention.InterventionID,
      Statut: statut,
      Date: intervention.DateIntervention,
    }
    this.interventionService.updateStatusIntervention(data).subscribe(result => {
      console.log(result);
      this.loadPlannings(this.technicienID);
    })
  }

  finalizeIntervention(){
    const data = {
      InterventionID: this.selectedIntervention.InterventionID,
      Statut: "Terminee",
      Date: this.selectedIntervention.DateIntervention,
      Heure: this.getCurrentTime(),
      Description: this.description
    }
    this.interventionService.updateStatusIntervention(data).subscribe(result => {
      console.log(result);
      this.loadPlannings(this.technicienID);
      this.closeModal();
    })
  }

  getCurrentTime = (): string => {
    const now = new Date();
  
    // Récupérer l'heure, les minutes et les secondes
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
  
    // Formater l'heure en format HH:MM:SS
    const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  
    return formattedTime;
  };
}
