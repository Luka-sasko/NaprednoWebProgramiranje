const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const methodOverride = require('method-override');
const session = require('express-session');
const passport = require('passport');
const LocalStrategy = require('passport-local').Strategy;
const bcrypt = require('bcryptjs');
const User = require('./models/user');
const flash = require('connect-flash');

const app = express();

// Konekcija na MongoDB (uklonjene zastarjele opcije)
mongoose.connect('mongodb://localhost:27017/lv6_projects');

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(methodOverride('_method'));
app.use(express.static('public'));

// Konfiguracija sesija
app.use(session({
  secret: 'nekiSigurniTajniKljuć12345',
  resave: false,
  saveUninitialized: true
}));

// Passport konfiguracija
app.use(passport.initialize());
app.use(passport.session());

// Connect-flash nakon sesija
app.use(flash());

// Postavljanje poruka u res.locals
app.use((req, res, next) => {
  res.locals.success_msg = req.flash('success_msg');
  res.locals.error_msg = req.flash('error_msg');
  res.locals.user = req.user;
  next();
});

// Passport konfiguracija
passport.use(new LocalStrategy(async (username, password, done) => {
  try {
    const user = await User.findOne({ username });
    if (!user) return done(null, false, { message: 'Ne postoji korisnik' });

    const match = await bcrypt.compare(password, user.password);
    return done(null, match ? user : false);
  } catch (err) {
    return done(err);
  }
}));
passport.serializeUser((user, done) => {
  done(null, user.id);
});
passport.deserializeUser(async (id, done) => {
  try {
    const user = await User.findById(id);
    done(null, user);
  } catch (err) {
    done(err, null);
  }
});

// Postavljanje view engine-a
app.set('view engine', 'jade');

// Rute
const authRouter = require('./routes/auth');
const projectsRouter = require('./routes/projects');

app.use('/', authRouter);
app.use('/projects', projectsRouter);

// Default ruta
app.get('/', (req, res) => {
  res.redirect('/projects');
});

// Pokretanje servera
app.listen(3000, () => console.log('Server running on http://localhost:3000'));