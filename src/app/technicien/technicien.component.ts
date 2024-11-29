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
import { Intervention, STATUS_TYPES, getStatusColor } from '../models/intervention.model';

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

  readonly STATUS_TYPES = STATUS_TYPES;

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
    console.log('TechnicienComponent initialized');
    this.loadInterventions();
  }

  private loadInterventions() {
    const currentUser = this.authService.currentUserValue;
    console.log('Current user:', currentUser);
    
    if (!currentUser?.technicienId) {
      this.error = 'Utilisateur non connecté ou non technicien';
      console.error('No technicienId found:', currentUser);
      return;
    }

    this.loading = true;
    this.error = '';

    console.log('Fetching interventions for technicienId:', currentUser.technicienId);

    this.interventionService.getTechnicienInterventions(currentUser.technicienId)
      .subscribe({
        next: (response) => {
          console.log('API Response:', response);
          if (response.status === 'success' && response.data) {
            this.interventions = response.data;
            console.log('Interventions loaded:', this.interventions);
            this.updateFilteredInterventions();
            this.updateCalendarEvents();
          } else {
            this.error = response.message || 'Erreur lors du chargement des interventions';
            console.error('API Error:', response);
          }
        },
        error: (error) => {
          this.error = 'Erreur lors du chargement des interventions: ' + error;
          console.error('HTTP Error:', error);
          this.loading = false;
        },
        complete: () => {
          this.loading = false;
          console.log('Request completed');
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
    console.log('Filtered interventions:', this.filteredInterventions);
  }

  updateCalendarEvents() {
    this.calendarOptions.events = this.interventions
      .filter(intervention => new Date(intervention.DebutIntervention) >= new Date())
      .map(intervention => ({
        title: `${intervention.TypeIntervention} - ${intervention.client?.Nom || 'Client ' + intervention.ClientID}`,
        start: intervention.DebutIntervention,
        end: intervention.FinIntervention,
        backgroundColor: getStatusColor(intervention.StatutIntervention)
      }));
    console.log('Calendar events updated:', this.calendarOptions.events);
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
    intervention.StatutIntervention = STATUS_TYPES.IN_PROGRESS;
    this.updateFilteredInterventions();
  }

  completeIntervention(intervention: Intervention) {
    intervention.StatutIntervention = STATUS_TYPES.COMPLETED;
    this.updateFilteredInterventions();
  }

  cancelIntervention(intervention: Intervention) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      intervention.StatutIntervention = STATUS_TYPES.CANCELLED;
      this.updateFilteredInterventions();
    }
  }
}