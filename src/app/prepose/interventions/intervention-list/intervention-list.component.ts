import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { InterventionService } from '../../../core/services/intervention.service';
import { TechnicienService } from '../../../core/services/technicien.service';
import { Intervention, STATUS_TYPES } from '../../../models/intervention.model';
import { Technicien } from '../../../models/technicien.model';
import { ApiResponse } from '../../../models/api.model';

@Component({
  selector: 'app-intervention-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    FormsModule
  ],
  templateUrl: './intervention-list.component.html',
  styleUrls: ['./intervention-list.component.css']
})
export class InterventionListComponent implements OnInit {
  interventions: Intervention[] = [];
  filteredInterventions: Intervention[] = [];
  techniciens: Technicien[] = [];
  selectedTechnicienId: number | null = null;
  searchTerm: string = '';
  filterValue: string = '';
  loading = false;
  error = '';
  showAssignModal = false;
  selectedIntervention: Intervention | null = null;

  readonly STATUS_TYPES = STATUS_TYPES;

  constructor(
    private interventionService: InterventionService,
    private technicienService: TechnicienService
  ) {}

  ngOnInit(): void {
    this.loadInterventions();
    this.loadTechniciens();
  }

  loadInterventions(): void {
    this.loading = true;
    this.error = '';
    
    this.interventionService.getAllInterventions().subscribe({
      next: (response: ApiResponse<Intervention[]>) => {
        if (response.status === 'success' && response.data) {
          this.interventions = response.data;
          this.updateFilteredInterventions();
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

  loadTechniciens(): void {
    this.technicienService.getAllTechniciens().subscribe({
      next: (response: ApiResponse<Technicien[]>) => {
        if (response.status === 'success' && response.data) {
          this.techniciens = response.data;
        }
      },
      error: (error: Error) => {
        console.error('Erreur lors du chargement des techniciens:', error);
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

  openAssignModal(intervention: Intervention): void {
    this.selectedIntervention = intervention;
    this.selectedTechnicienId = null;
    this.showAssignModal = true;
  }

  closeAssignModal(): void {
    this.showAssignModal = false;
    this.selectedIntervention = null;
    this.selectedTechnicienId = null;
  }

  assignerTechnicien(): void {
    if (!this.selectedIntervention || !this.selectedTechnicienId) {
      return;
    }

    this.loading = true;
    this.interventionService.assignerTechnicien(
      this.selectedIntervention.InterventionID!,
      this.selectedTechnicienId
    ).subscribe({
      next: (response: ApiResponse<void>) => {
        if (response.status === 'success') {
          this.loadInterventions();
          this.closeAssignModal();
        } else {
          this.error = response.message || 'Erreur lors de l\'assignation';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        this.error = 'Erreur lors de l\'assignation: ' + error.message;
        this.loading = false;
      }
    });
  }
}