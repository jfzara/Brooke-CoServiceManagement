import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],  // Assurez-vous d'importer RouterOutlet pour le routage
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']  // Assurez-vous d'utiliser 'styleUrls' au lieu de 'styleUrl'
})
export class AppComponent {
  title = 'frontend-standalone';
}