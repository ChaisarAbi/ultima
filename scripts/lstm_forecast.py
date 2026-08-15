#!/usr/bin/env python3
"""
ARIMA-based forecasting script using statsmodels as TensorFlow is not available.
Replaces LSTM with SARIMAX for time series forecasting.
"""

import sys
import argparse
import pandas as pd
import numpy as np
import json
import os
import warnings
warnings.filterwarnings('ignore')

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--input', required=True, help='Path to input CSV')
    parser.add_argument('--output', required=True, help='Path to output JSON')
    parser.add_argument('--steps', type=int, default=7, help='Number of forecast steps')
    args = parser.parse_args()

    # 1. Load Data
    df = pd.read_csv(args.input, parse_dates=['log_date'])
    df = df.sort_values('log_date')
    data = df['value'].values

    if len(data) < 10:
        raise ValueError(f"Data terlalu sedikit: {len(data)} titik data. Minimal 10.")

    # 2. Try ARIMA, fallback to naive forecast if it fails
    try:
        from statsmodels.tsa.arima.model import ARIMA
        
        # Find best ARIMA order using AIC
        best_aic = np.inf
        best_order = (1, 1, 1)
        
        # Try a few orders
        for p in range(0, 4):
            for d in range(0, 2):
                for q in range(0, 4):
                    try:
                        model = ARIMA(data, order=(p, d, q))
                        fitted = model.fit()
                        if fitted.aic < best_aic:
                            best_aic = fitted.aic
                            best_order = (p, d, q)
                    except:
                        continue
        
        # Fit best model
        model = ARIMA(data, order=best_order)
        fitted = model.fit()
        
        # Forecast - statsmodels returns array directly in newer versions
        forecast = fitted.forecast(steps=args.steps)
        predictions = np.asarray(forecast).flatten()
        
        print(f"ARIMA model fitted with order {best_order}, AIC: {best_aic:.2f}", file=sys.stderr)
        
    except Exception as e:
        print(f"ARIMA failed ({str(e)}), using naive seasonal forecast.", file=sys.stderr)
        # Simple seasonal naive forecast
        seasonal_period = min(7, len(data) // 2)
        if seasonal_period < 1:
            seasonal_period = 1
        
        # Use last seasonal_period values repeated
        last_season = data[-seasonal_period:]
        predictions = np.tile(last_season, int(np.ceil(args.steps / seasonal_period)))[:args.steps]

    # 3. Ensure non-negative predictions
    predictions = np.maximum(predictions, 0)

    # 4. Generate future dates
    last_date = df['log_date'].iloc[-1]
    future_dates = pd.date_range(start=last_date + pd.Timedelta(days=1), periods=args.steps)

    # 5. Save JSON
    result = [
        {'date': d.strftime('%Y-%m-%d'), 'value': round(float(v), 2)}
        for d, v in zip(future_dates, predictions)
    ]

    with open(args.output, 'w') as f:
        json.dump(result, f)

    print(f"Successfully generated {len(result)} predictions.", file=sys.stderr)

if __name__ == "__main__":
    main()