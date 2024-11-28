import { Component, OnInit } from '@angular/core';
import { FullCalendarModule } from '@fullcalendar/angular';
import { CalendarOptions } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { InterventionService, Intervention, STATUS_TYPES, getStatusColor } from '../services/intervention.service';

@Component({
  selector: 'app-technicien',
  standalone: true,
  imports: [FullCalendarModule, CommonModule, FormsModule],
  templateUrl: './technicien.component.html',
  styleUrls: ['./technicien.component.css']
})
export class TechnicienComponent implements OnInit {
  interventions: Intervention[] = [];
  filteredInterventions: Intervention[] = [];
  searchTerm: string = '';
  filterValue: string = '';
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

  constructor(private interventionService: InterventionService) {}

  ngOnInit() {
    this.loadInterventions();
  }

  private loadInterventions() {
    const technicienId = 2; // À remplacer par l'ID du technicien connecté
    this.interventionService.getPlanningByTechnicienWithMoreInfos(technicienId)
      .subscribe(interventions => {
        this.interventions = interventions;
        this.updateFilteredInterventions();
        this.updateCalendarEvents();
      });
  }

  updateFilteredInterventions() {
    this.filteredInterventions = this.interventions.filter(intervention => {
      const matchesSearch = this.searchTerm === '' || 
        Object.values(intervention).some(value => 
          value?.toString().toLowerCase().includes(this.searchTerm.toLowerCase())
        );

      const matchesFilter = this.filterValue === '' ||
        (this.filterValue === 'pending' && intervention.StatutIntervention === STATUS_TYPES.PENDING) ||
        (this.filterValue === 'inProgress' && intervention.StatutIntervention === STATUS_TYPES.IN_PROGRESS) ||
        (this.filterValue === 'completed' && intervention.StatutIntervention === STATUS_TYPES.COMPLETED);

      return matchesSearch && matchesFilter;
    });
  }

  updateCalendarEvents() {
    this.calendarOptions.events = this.interventions
      .filter(intervention => new Date(intervention.DebutIntervention) >= new Date())
      .map(intervention => ({
        title: `${intervention.TypeIntervention} - Client ${intervention.ClientID}`,
        start: intervention.DebutIntervention,
        end: intervention.FinIntervention,
        backgroundColor: getStatusColor(intervention.StatutIntervention)
      }));
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
    this.interventionService.updateStatusIntervention({
      InterventionID: intervention.InterventionID!,
      StatutIntervention: STATUS_TYPES.IN_PROGRESS
    }).subscribe(() => {
      this.loadInterventions();
    });
  }

  completeIntervention(intervention: Intervention) {
    this.interventionService.updateStatusIntervention({
      InterventionID: intervention.InterventionID!,
      StatutIntervention: STATUS_TYPES.COMPLETED
    }).subscribe(() => {
      this.loadInterventions();
    });
  }

  cancelIntervention(intervention: Intervention) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) {
      this.interventionService.deleteIntervention(intervention.InterventionID!)
        .subscribe(() => {
          this.loadInterventions();
        });
    }
  }
}