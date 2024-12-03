import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TechnicienService } from '../../../core/services/technicien.service';
import { Technicien } from '../../../models/technicien.model';
import { ApiResponse } from '../../../models/api.model';

@Component({
  selector: 'app-technicien-selector',
  templateUrl: './technicien-selector.component.html',
  styleUrls: ['./technicien-selector.component.css'],
  standalone: true,
  imports: [CommonModule]
})
export class TechnicienSelectorComponent implements OnInit {
  @Input() selectedTechnicienId: number | null = null;
  @Output() technicienSelected = new EventEmitter<number>();
  
  techniciens: Technicien[] = [];
  loading = false;
  error: string | null = null;

  constructor(private technicienService: TechnicienService) {}

  ngOnInit(): void {
    this.loadTechniciens();
  }

  loadTechniciens(): void {
    this.loading = true;
    this.error = null;
    
    this.technicienService.getAllTechniciens().subscribe({
      next: (response: ApiResponse<Technicien[]>) => {
        if (response.status === 'success' && response.data) {
          this.techniciens = response.data;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Erreur lors du chargement des techniciens';
        this.loading = false;
        console.error('Erreur:', err);
      }
    });
  }

  onTechnicienSelect(event: Event): void {
    const select = event.target as HTMLSelectElement;
    const technicienId = parseInt(select.value, 10);
    if (!isNaN(technicienId)) {
      this.technicienSelected.emit(technicienId);
    }
  }
}