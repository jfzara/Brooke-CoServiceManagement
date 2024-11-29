import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { Intervention } from '../models/intervention.model';

export interface ApiResponse<T> {
  status: 'success' | 'error';
  data?: T;
  message?: string;
}

@Injectable({
  providedIn: 'root'
})
export class InterventionService {
  private apiUrl = 'http://localhost:8000/api.php';

  constructor(private http: HttpClient) { }

  getTechnicienInterventions(technicienId: number): Observable<ApiResponse<Intervention[]>> {
    console.log('Récupération des interventions pour le technicien:', technicienId);
    return this.http.get<ApiResponse<Intervention[]>>(
      `${this.apiUrl}?action=get_technicien_interventions&technicienId=${technicienId}`
    ).pipe(
      tap(response => console.log('Réponse API:', response)),
      catchError(error => {
        console.error('Erreur API:', error);
        throw error;
      })
    );
  }

  getPlanningByTechnicienWithMoreInfos(technicienId: number): Observable<ApiResponse<Intervention[]>> {
    return this.getTechnicienInterventions(technicienId);
  }

  updateStatusIntervention(interventionId: number, status: string): Observable<ApiResponse<void>> {
    console.log('Mise à jour du statut de l\'intervention:', { interventionId, status });
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_intervention_status`,
      {
        InterventionID: interventionId,
        StatutIntervention: status
      }
    ).pipe(
      tap(response => console.log('Réponse de mise à jour du statut:', response)),
      catchError(error => {
        console.error('Erreur de mise à jour du statut:', error);
        throw error;
      })
    );
  }

  getInterventionById(id: number): Observable<ApiResponse<Intervention>> {
    console.log('Récupération de l\'intervention:', id);
    return this.http.get<ApiResponse<Intervention>>(
      `${this.apiUrl}?action=get_intervention&id=${id}`
    ).pipe(
      tap(response => console.log('Détails de l\'intervention:', response)),
      catchError(error => {
        console.error('Erreur lors de la récupération de l\'intervention:', error);
        throw error;
      })
    );
  }

  updateIntervention(id: number, data: Partial<Intervention>): Observable<ApiResponse<void>> {
    console.log('Mise à jour de l\'intervention:', { id, data });
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_intervention`,
      { id, ...data }
    ).pipe(
      tap(response => console.log('Réponse de mise à jour:', response)),
      catchError(error => {
        console.error('Erreur de mise à jour:', error);
        throw error;
      })
    );
  }

  deleteIntervention(id: number): Observable<ApiResponse<void>> {
    console.log('Suppression de l\'intervention:', id);
    return this.http.delete<ApiResponse<void>>(
      `${this.apiUrl}?action=delete_intervention&id=${id}`
    ).pipe(
      tap(response => console.log('Réponse de suppression:', response)),
      catchError(error => {
        console.error('Erreur de suppression:', error);
        throw error;
      })
    );
  }
}