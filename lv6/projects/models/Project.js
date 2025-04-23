const mongoose = require('mongoose');

const ProjectSchema = new mongoose.Schema({
  naziv: String,
  opis: String,
  cijena: Number,
  obavljeniPoslovi: String,
  datumPocetka: Date,
  datumZavrsetka: Date,
  clanovi: [String]
});

module.exports = mongoose.model('Project', ProjectSchema);
