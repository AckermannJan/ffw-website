const fs = require("fs");

// Function to convert CSV to JSON
const csvToJson = (csvFilePath, jsonFilePath) => {
  // Read the CSV file
  const csvData = fs.readFileSync(csvFilePath, "utf-8");

  // Split the CSV data into rows
  const rows = csvData.trim().split("\n");

  // Get the headers from the first row and trim \r characters
  const headers = rows[0].replace(/\r/g, "").split(",");

  // Initialize an array to store the JSON objects
  const jsonData = [];

  // Process each row starting from the second row
  for (let i = 1; i < rows.length; i++) {
    const row = rows[i].replace(/\r/g, "").split(",");
    const jsonObj = {};

    // Create a JSON object using the headers and row data
    for (let j = 0; j < headers.length; j++) {
      jsonObj[headers[j]] = row[j];
    }

    // Add the JSON object to the array
    jsonData.push(jsonObj);
  }

  // Write the JSON data to a file
  fs.writeFileSync(jsonFilePath, JSON.stringify(jsonData, null, 2));

  console.log("Conversion complete: CSV to JSON");
};

// Example usage
const csvFilePath = "dienstplan.csv"; // Replace with your CSV file path
const jsonFilePath = "output.json"; // Replace with your desired JSON output file path

csvToJson(csvFilePath, jsonFilePath);
