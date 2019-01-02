import { Component, OnInit } from '@angular/core';
import { QuoteService } from './quote.service';
import { Quote } from './Quote';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
    public currentQuote: Quote = null;

    public constructor(
        private quoteService: QuoteService) {
    }

    public ngOnInit(): void {
        this.quoteService.getRandomQuote()
        .then(quote => {
            this.currentQuote = quote;
        });
    }
}
