// path: src/pages/Contact.js
import React, { useState } from "react";
import "./Contact.css";

/*
  דף "Contact":
  - טופס פרטים בסיסי ללא שליחה לשרת (דמו).
  - אימות מינימלי בצד לקוח + הודעת תודה.
*/
export default function Contact(){
  const [name,setName]=useState("");
  const [email,setEmail]=useState("");
  const [topic,setTopic]=useState("General");
  const [msg,setMsg]=useState("");
  const [sent,setSent]=useState(false);
  const [err,setErr]=useState("");

  // אימות שדות פשוט
  const validate=()=>{
    if(!name.trim()) return "Please enter your name.";
    if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) return "Please enter a valid email.";
    if(!msg.trim()) return "Please write your message.";
    return "";
  };

  const onSubmit=(e)=>{
    e.preventDefault();
    const eMsg=validate();
    if(eMsg){ setErr(eMsg); return; }
    setErr(""); setSent(true);
  };

  return (
    <div className="c-wrap">
      <h1>Contact Us</h1>
      <p className="c-lead">Questions, feedback, or collaboration ideas? We’d love to hear from you.</p>

      {!sent ? (
        <form className="c-form" onSubmit={onSubmit} noValidate>
          <div className="c-row">
            <label className="c-lbl">Name</label>
            <input className="c-input" value={name} onChange={e=>setName(e.target.value)} placeholder="Your name"/>
          </div>

          <div className="c-row">
            <label className="c-lbl">Email</label>
            <input className="c-input" type="email" value={email} onChange={e=>setEmail(e.target.value)} placeholder="you@example.com"/>
          </div>

          <div className="c-row">
            <label className="c-lbl">Topic</label>
            <select className="c-input" value={topic} onChange={e=>setTopic(e.target.value)}>
              <option>General</option>
              <option>Product Feedback</option>
              <option>Bug Report</option>
              <option>Partnership</option>
            </select>
          </div>

          <div className="c-row">
            <label className="c-lbl">Message</label>
            <textarea className="c-input c-textarea" value={msg} onChange={e=>setMsg(e.target.value)} placeholder="Type your message here..."/>
          </div>

          {err && <div className="c-err">{err}</div>}

          <div className="c-actions">
            <button type="submit" className="c-btn c-btn-primary">Send</button>
            <button type="button" className="c-btn" onClick={()=>{setName("");setEmail("");setTopic("General");setMsg("");setErr("");}}>Clear</button>
          </div>
        </form>
      ) : (
        <div className="c-thanks">
          <h3>Thanks!</h3>
          <p>We received your message and will get back to you soon.</p>
          <button className="c-btn" onClick={()=>{setSent(false);}}>Send another</button>
        </div>
      )}

      <div className="c-aside">
        <div className="c-card">
          <h4>Support</h4>
          <p>Email: <a href="mailto:support@tripmaster.app">support@tripmaster.app</a></p>
          <p>Docs: <a href="#">docs.tripmaster.app</a></p>
        </div>
        <div className="c-card">
          <h4>Company</h4>
          <p>TripMaster, 123 Anywhere St, TLV</p>
          <p>Mon–Fri, 9:00–18:00</p>
        </div>
      </div>
    </div>
  );
}
