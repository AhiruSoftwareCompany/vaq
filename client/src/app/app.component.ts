import { Component, OnInit } from '@angular/core';
import { QuoteService } from './quote.service';
import { Quote } from './Quote';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
    public currentQuote: Quote;

    public constructor(
        private quoteService: QuoteService) {
    }

    public ngOnInit(): void {
        this.currentQuote = this.quoteService.getRandomQuote();
    }
}
