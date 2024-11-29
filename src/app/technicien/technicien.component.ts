import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { InterventionService } from '../services/intervention.service';
import { AuthService } from '../services/auth.service';
import { FullCalendarModule } from '@fullcalendar/angular';
import { CalendarOptions } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
import { Intervention, STATUS_TYPES, getStatusColor } from '../models/intervention.model';
import { UserProfile } from '../models/user.model';

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
  userProfile: UserProfile;

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
    private authService: AuthService,
    private router: Router
  ) {
    this.userProfile = this.authService.userProfile;
  }

  ngOnInit() {
    this.loadInterventions();
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  private loadInterventions() {
    if (!this.userProfile?.technicienId) {
      this.error = 'Utilisateur non connecté ou non technicien';
      return;
    }

    this.loading = true;
    this.error = '';

    this.interventionService.getTechnicienInterventions(this.userProfile.technicienId)
      .subscribe({
        next: (response) => {
          if (response.status === 'success' && response.data) {
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
      const searchFields = [
        intervention.TypeIntervention,
        intervention.Description,
        intervention.ClientID?.toString(),
        new Date(intervention.DebutIntervention).toLocaleDateString(),
        intervention.StatutIntervention
      ];

      const matchesSearch = !this.searchTerm || 
        searchFields.some(field => 
          field?.toLowerCase().includes(this.searchTerm.toLowerCase())
        );

      const matchesFilter = !this.filterValue || 
        intervention.StatutIntervention.toLowerCase() === this.filterValue.toLowerCase();

      return matchesSearch && matchesFilter;
    });
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
    if (confirm('Voulez-vous démarrer cette intervention ?')) {
      intervention.StatutIntervention = STATUS_TYPES.IN_PROGRESS;
      this.updateFilteredInterventions();
    }
  }

  completeIntervention(intervention: Intervention) {
    if (confirm('Voulez-vous marquer cette intervention comme terminée ?')) {
      intervention.StatutIntervention = STATUS_TYPES.COMPLETED;
      this.updateFilteredInterventions();
    }
  }

  cancelIntervention(intervention: Intervention) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      intervention.StatutIntervention = STATUS_TYPES.CANCELLED;
      this.updateFilteredInterventions();
    }
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
  }
}