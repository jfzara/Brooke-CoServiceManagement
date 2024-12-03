import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { InterventionService } from '../core/services/intervention.service';
import { AuthService } from '../core/services/auth.service';
import { FullCalendarModule } from '@fullcalendar/angular';
import { CalendarOptions } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
import { Intervention, STATUS_TYPES, getStatusColor } from '../models/intervention.model';
import { UserProfile } from '../models/user.model';
import { ApiResponse } from '../models/api.model';

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

  ngOnInit(): void {
    this.loadInterventions();
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  private loadInterventions(): void {
    if (!this.userProfile?.technicienId) {
      this.error = 'Utilisateur non connecté ou non technicien';
      return;
    }

    this.loading = true;
    this.error = '';

    this.interventionService.getTechnicienInterventions(this.userProfile.technicienId)
      .subscribe({
        next: (response: ApiResponse<Intervention[]>) => {
          if (response.status === 'success' && response.data) {
            this.interventions = response.data;
            this.updateFilteredInterventions();
            this.updateCalendarEvents();
          } else {
            this.error = response.message || 'Erreur lors du chargement des interventions';
          }
          this.loading = false;
        },
        error: (error: Error) => {
          this.error = 'Erreur lors du chargement des interventions: ' + error.message;
          this.loading = false;
        }
      });
  }

  updateFilteredInterventions(): void {
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

  onSearch(event: Event): void {
    const target = event.target as HTMLInputElement;
    this.searchTerm = target.value;
    this.updateFilteredInterventions();
  }

  onFilterChange(event: Event): void {
    const target = event.target as HTMLSelectElement;
    this.filterValue = target.value;
    this.updateFilteredInterventions();
  }

  startIntervention(intervention: Intervention): void {
    if (confirm('Voulez-vous démarrer cette intervention ?')) {
      this.updateInterventionStatus(intervention, STATUS_TYPES.IN_PROGRESS);
    }
  }

  completeIntervention(intervention: Intervention): void {
    if (confirm('Voulez-vous marquer cette intervention comme terminée ?')) {
      this.updateInterventionStatus(intervention, STATUS_TYPES.COMPLETED);
    }
  }

  cancelIntervention(intervention: Intervention): void {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      this.updateInterventionStatus(intervention, STATUS_TYPES.CANCELLED);
    }
  }

  private updateInterventionStatus(intervention: Intervention, status: string): void {
    if (intervention.InterventionID) {
      this.interventionService.updateStatusIntervention(intervention.InterventionID, status)
        .subscribe({
          next: (response: ApiResponse<void>) => {
            if (response.status === 'success') {
              intervention.StatutIntervention = status;
              this.updateFilteredInterventions();
              this.updateCalendarEvents();
            } else {
              this.error = response.message || 'Erreur lors de la mise à jour du statut';
            }
          },
          error: (error: Error) => {
            this.error = 'Erreur lors de la mise à jour du statut: ' + error.message;
          }
        });
    }
  }

  updateCalendarEvents(): void {
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