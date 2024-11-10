import { Component } from '@angular/core';
import { FullCalendarModule } from '@fullcalendar/angular'; // Importez FullCalendarModule
import { CalendarOptions } from '@fullcalendar/core'; // Importez les options du calendrier
import dayGridPlugin from '@fullcalendar/daygrid'; // Importez le plugin dayGrid
import interactionPlugin from '@fullcalendar/interaction'; // Importez le plugin interaction
import frLocale from '@fullcalendar/core/locales/fr'; // Importez la locale française

@Component({
  selector: 'app-prepose',
  standalone: true,
  imports: [FullCalendarModule], // Ajoutez FullCalendarModule ici
  templateUrl: './prepose.component.html',
  styleUrls: ['./prepose.component.css']
})
export class PreposeComponent {
  calendarOptions: CalendarOptions = {
    locale: frLocale,  // Utilisez la locale française
    plugins: [dayGridPlugin, interactionPlugin],  // Ajoutez les plugins nécessaires
    initialView: 'dayGridMonth',  // Vue par défaut
    headerToolbar: {
      left: 'prev,next today',  // Boutons de navigation
      center: 'title',  // Affiche le titre
      right: 'dayGridMonth,dayGridWeek,dayGridDay'  // Options de vue
    }
  };
}