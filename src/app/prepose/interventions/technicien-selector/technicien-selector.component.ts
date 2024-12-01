import { Component, EventEmitter, Input, Output, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TechnicienDisponibiliteService } from '../../../core/services/technicien-disponibilite.service';
import { TechnicienService } from '../../../core/services/technicien.service';
import { Technicien, TechnicienDisponibilite } from '../../../models/technicien.model';
import { ApiResponse } from '../../../models/api.model';

@Component({
  selector: 'app-technicien-selector',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="technicien-selector">
      <div class="form-group">
        <label>Sélectionner un technicien disponible</label>
        <select 
          class="form-control" 
          [(ngModel)]="selectedTechnicienId"
          (ngModelChange)="onTechnicienSelected($event)"
        >
          <option [ngValue]="null">Sélectionnez un technicien</option>
          <option *ngFor="let technicien of techniciens" 
                  [value]="technicien.TechnicienID"
                  [disabled]="!isTechnicienDisponible(technicien.TechnicienID)">
            {{technicien.Nom}} {{technicien.Prenom}}
          </option>
        </select>
      </div>
    </div>
  `,
  styles: [`
    .technicien-selector {
      margin: 1rem 0;
    }
    .form-control {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
  `]
})
export class TechnicienSelectorComponent implements OnInit {
  @Input() dateDebut?: string;
  @Input() dateFin?: string;
  @Output() technicienSelected = new EventEmitter<number>();

  techniciens: Technicien[] = [];
  techniciensDisponibles: TechnicienDisponibilite[] = [];
  selectedTechnicienId: number | null = null;

  constructor(
    private technicienService: TechnicienService,
    private disponibiliteService: TechnicienDisponibiliteService
  ) {}

  ngOnInit() {
    this.loadTechniciens();
    if (this.dateDebut && this.dateFin) {
      this.loadTechniciensDisponibles();
    }
  }

  private loadTechniciens() {
    this.technicienService.getAllTechniciens().subscribe({
      next: (response: ApiResponse<Technicien[]>) => {
        if (response.status === 'success' && response.data) {
          this.techniciens = response.data;
        }
      }
    });
  }

  private loadTechniciensDisponibles() {
    if (this.dateDebut && this.dateFin) {
      this.disponibiliteService.getTechniciensDisponibles(this.dateDebut, this.dateFin)
        .subscribe({
          next: (response: ApiResponse<TechnicienDisponibilite[]>) => {
            if (response.status === 'success' && response.data) {
              this.techniciensDisponibles = response.data;
            }
          }
        });
    }
  }

  isTechnicienDisponible(technicienId: number): boolean {
    return this.techniciensDisponibles.some(t => t.TechnicienID === technicienId && t.disponible);
  }

  onTechnicienSelected(technicienId: number | null) {
    if (technicienId) {
      this.technicienSelected.emit(technicienId);
    }
  }
}