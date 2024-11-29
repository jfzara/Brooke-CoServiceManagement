import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { InterventionService } from '../services/intervention.service';
import { AuthService } from '../services/auth.service';
import { FullCalendarModule } from '@fullcalendar/angular';
import { CalendarOptions } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';

export interface Intervention {
  InterventionID: number;
  TechnicienID: number;
  ClientID: number;
  TypeIntervention: string;
  Description: string;
  DebutIntervention: string;
  FinIntervention: string;
  StatutIntervention: string;
  Commentaires: string;
  client?: {
    Nom: string;
    Prenom: string;
    Adresse: string;
    Telephone: string;
  };
}

@Component({
  selector: 'app-technicien',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    RouterModule,
    FullCalendarModule
  ],
  templateUrl: './technicien.component.html',
  styleUrls: ['./technicien.component.css']
})
export class TechnicienComponent implements OnInit {
  interventions: Intervention[] = [];
  filteredInterventions: Intervention[] = [];
  searchTerm: string = '';
  filterValue: string = '';
  loading = false;
  error = '';

  readonly STATUS_TYPES = {
    PENDING: 'En attente',
    IN_PROGRESS: 'En cours',
    COMPLETED: 'Terminé',
    CANCELLED: 'Annulé'
  };

  calendarOptions: CalendarOptions = {
    locale: frLocale,
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,dayGridWeek,dayGridDay'
    },
    events: [],
    validRange: {
      start: new Date()
    }
  };

  constructor(
    private interventionService: InterventionService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.loadInterventions();
  }

  private loadInterventions() {
    const currentUser = this.authService.currentUserValue;
    if (!currentUser?.technicienId) {
      this.error = 'Utilisateur non connecté ou non technicien';
      return;
    }

    this.loading = true;
    this.error = '';

    this.interventionService.getTechnicienInterventions(currentUser.technicienId)
      .subscribe({
        next: (response) => {
          if (response.status === 'success') {
            this.interventions = response.data;
            this.updateFilteredInterventions();
            this.updateCalendarEvents();
          } else {
            this.error = response.message || 'Erreur lors du chargement des interventions';
          }
        },
        error: (error) => {
          this.error = 'Erreur lors du chargement des interventions: ' + error;
          this.loading = false;
        },
        complete: () => {
          this.loading = false;
        }
      });
  }

  updateFilteredInterventions() {
    this.filteredInterventions = this.interventions.filter(intervention => {
      const matchesSearch = this.searchTerm === '' || 
        Object.values(intervention).some(value => 
          value?.toString().toLowerCase().includes(this.searchTerm.toLowerCase())
        );

      const matchesFilter = this.filterValue === '' ||
        intervention.StatutIntervention.toLowerCase() === this.filterValue.toLowerCase();

      return matchesSearch && matchesFilter;
    });
  }

  updateCalendarEvents() {
    this.calendarOptions.events = this.interventions
      .filter(intervention => new Date(intervention.DebutIntervention) >= new Date())
      .map(intervention => ({
        title: `${intervention.TypeIntervention} - ${intervention.client?.Nom || 'Client ' + intervention.ClientID}`,
        start: intervention.DebutIntervention,
        end: intervention.FinIntervention,
        backgroundColor: this.getStatusColor(intervention.StatutIntervention)
      }));
  }

  getStatusColor(status: string): string {
    switch (status.toLowerCase()) {
      case this.STATUS_TYPES.PENDING.toLowerCase():
        return '#FFD700';
      case this.STATUS_TYPES.IN_PROGRESS.toLowerCase():
        return '#4CAF50';
      case this.STATUS_TYPES.COMPLETED.toLowerCase():
        return '#2196F3';
      case this.STATUS_TYPES.CANCELLED.toLowerCase():
        return '#f44336';
      default:
        return '#9E9E9E';
    }
  }

  onSearch(event: any) {
    this.searchTerm = event.target.value;
    this.updateFilteredInterventions();
  }

  onFilterChange(event: any) {
    this.filterValue = event.target.value;
    this.updateFilteredInterventions();
  }

  startIntervention(intervention: Intervention) {
    intervention.StatutIntervention = this.STATUS_TYPES.IN_PROGRESS;
    this.updateFilteredInterventions();
  }

  completeIntervention(intervention: Intervention) {
    intervention.StatutIntervention = this.STATUS_TYPES.COMPLETED;
    this.updateFilteredInterventions();
  }

  cancelIntervention(intervention: Intervention) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      intervention.StatutIntervention = this.STATUS_TYPES.CANCELLED;
      this.updateFilteredInterventions();
    }
  }
}